<?php

namespace App\Review;

use App\Communication\Notification\ReviewNotificationService;
use App\Entity\Concept;
use App\Entity\Contracts\ReviewableInterface;
use App\Entity\PendingChange;
use App\Entity\RelationType;
use App\Entity\Review;
use App\Entity\StudyArea;
use App\Entity\User;
use App\Repository\PendingChangeRepository;
use App\Review\Exception\InvalidChangeException;
use App\Review\Model\PendingChangeObjectInfo;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\Mapping\MappingException;
use Drenso\Shared\Exception\NullGuard\ObjectRequiredException;
use InvalidArgumentException;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

use function array_diff;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_unique;
use function array_values;
use function assert;
use function count;
use function in_array;
use function is_numeric;
use function is_string;
use function json_decode;
use function json_encode;
use function sprintf;

class ReviewService
{
  // Serializer details
  /** @var SerializerInterface|null */
  private static ?Serializer $serializer        = null;
  private const string SERIALIZER_FORMAT        = 'json';

  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly PendingChangeRepository $pendingChangeRepository,
    private readonly ReviewNotificationService $reviewNotificationService,
    private readonly ValidatorInterface $validator,
    private readonly RequestStack $requestStack,
    private readonly TranslatorInterface $translator,
    private readonly Security $security)
  {
  }

  /** Return whether review mode is enabled for this object type. */
  public function isReviewModeEnabledForObject(StudyArea $studyArea, ReviewableInterface $object): bool
  {
    if (!$studyArea->isReviewModeEnabled()) {
      return false;
    }

    // Currently, review mode is only enabled for concept
    return $object instanceof Concept;
  }

  /**
   * Retrieve the original object linked to the pending change.
   *
   * @throws EntityNotFoundException|InvalidChangeException
   */
  public function getOriginalObject(PendingChange $pendingChange): ReviewableInterface
  {
    if (null === $pendingChange->getObjectId()) {
      throw new InvalidChangeException($pendingChange);
    }

    // Retrieve the object as referenced by the change
    $object = $this->entityManager->getRepository($pendingChange->getObjectType())->find($pendingChange->getObjectId());
    if (!$object) {
      // The object belonging with the review does not exist, this is an error
      throw new EntityNotFoundException();
    }
    assert($object instanceof ReviewableInterface);

    return $object;
  }

  /**
   * This method creates the pending change in the database, by detecting the changed fields in the given object,
   * based on the snapshot that is supplied (which needs to be created by this service).
   *
   * Note that after calling this method, the entity manager will be cleared!
   *
   * The exceptions can be thrown, but are unlikely. We do not want these exceptions to propagate to every controller.
   *
   * @param string|null $originalDataSnapshot Can be null in case of remove
   *
   * @noinspection PhpDocMissingThrowsInspection
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function storeChange(
    StudyArea $studyArea,
    ReviewableInterface $object,
    string $changeType,
    ?string $originalDataSnapshot = null,
    ?callable $directCallback = null): void
  {
    if (!in_array($changeType, PendingChange::CHANGE_TYPES)) {
      throw new InvalidArgumentException(sprintf('Supplied change type %s is not valid', $changeType));
    }

    // Check for review mode: when not enabled, do the direct save
    if (!$this->isReviewModeEnabledForObject($studyArea, $object)) {
      $this->directSave($object, $changeType, $directCallback);

      return;
    }

    // Create the pending change entity
    $pendingChange = new PendingChange()
      ->setStudyArea($studyArea)
      ->setChangeType($changeType)
      ->setObject($object)
      ->setObjectId($object->getId())
      ->setObjectType($object->getReviewName())
      ->setChangedFields([])
      ->setOwner($this->getUser());

    if ($changeType !== PendingChange::CHANGE_TYPE_REMOVE) {
      $pendingChange->setChangedFields($this->determineChangedFieldsFromSnapshot($object, $originalDataSnapshot));
    }

    // If nothing has changed, we have nothing to do for review and we use the original behavior
    if ($changeType !== PendingChange::CHANGE_TYPE_REMOVE && 0 === count((array)$pendingChange->getChangedFields())) {
      // Use the normal save behavior
      $this->directSave($object, $changeType, $directCallback);

      return;
    }

    // Validate the entity
    if (count($violations = $this->validator->validate($pendingChange))) {
      assert($violations instanceof ConstraintViolationList);
      throw new InvalidArgumentException(sprintf('Pending change validation not passed! %s', $violations));
    }

    // Clean object from doctrine state
    // This breaks the state of currently loaded object, which is why we replace the existing relations in the
    // PendingChange with doctrine references
    $this->entityManager->clear();

    // Replace the relations after the manager has been cleared
    $refOwner     = $this->entityManager->getReference(User::class, $pendingChange->getOwner()->getId());
    $refStudyArea = $this->entityManager->getReference(StudyArea::class, $pendingChange->getStudyArea()->getId());
    assert($refOwner instanceof User);
    assert($refStudyArea instanceof StudyArea);
    $pendingChange->setOwner($refOwner);
    $pendingChange->setStudyArea($refStudyArea);

    // Merge the new pending change with an existing one, if any
    if ($mergeable = $this->pendingChangeRepository->getMergeable($pendingChange)) {
      $pendingChange = $mergeable->merge($pendingChange);
    }

    // Store the pending change
    $this->entityManager->persist($pendingChange);
    $this->entityManager->flush($pendingChange);

    // Add flash notification about the review change
    $this->addFlash('notice', $this->translator->trans('review.saved-for-review'));
  }

  /**
   * @throws ORMException
   * @throws OptimisticLockException
   * @throws MappingException
   */
  public function updateChange(
    StudyArea $studyArea,
    ReviewableInterface $object,
    PendingChange $pendingChange,
    ?string $originalDataSnapshot = null): void
  {
    if ($pendingChange->getChangeType() === PendingChange::CHANGE_TYPE_REMOVE) {
      throw new InvalidArgumentException('Remove changes cannot be updated!');
    }

    // Check for review mode: when not enabled, do the direct save
    if (!$this->isReviewModeEnabledForObject($studyArea, $pendingChange->getObject())) {
      throw new InvalidArgumentException('Changes cannot be updated for types that are not enabled');
    }

    // Determine the new change fields
    $changedFields = array_unique(array_merge(
      $pendingChange->getChangedFields(),
      $this->determineChangedFieldsFromSnapshot($object, $originalDataSnapshot),
    ));

    // Update the pending change
    $pendingChange
      ->setObject($object)
      ->setChangedFields($changedFields);

    // Validate the entity
    if (count($violations = $this->validator->validate($pendingChange))) {
      assert($violations instanceof ConstraintViolationList);
      throw new InvalidArgumentException(sprintf('Pending change validation not passed! %s', $violations));
    }

    // Clear the entity manager
    $this->entityManager->clear();

    // Reapply the changes in a fresh pending change object, as we just cleared the EM
    /** @var PendingChange $pendingChange */
    $pendingChange = $this->pendingChangeRepository->find($pendingChange->getId()) ?? throw new ObjectRequiredException();
    $pendingChange->setObject($object)
      ->setChangedFields($changedFields);

    // If the review was already reviewed, clear its approval status
    $review = $pendingChange->getReview();
    if ($review) {
      $review
        ->setApprovedAt(null)
        ->setApprovedBy(null);
    }

    // Store the updated pending change
    $this->entityManager->flush($pendingChange);
    $this->entityManager->flush($review);

    // Add flash notification about the review change
    $this->addFlash('notice', $this->translator->trans('review.saved-for-review'));
  }

  /** Retrieve the data snapshot used for change detection. */
  public function getSnapshot(ReviewableInterface $object): string
  {
    return self::getDataSnapshot($object);
  }

  /**
   * Retrieve the fields which can not be edited by this user
   * Currently, we ignore the user.
   *
   * @param PendingChange|null $exclude Exclude this pending change from the object information
   */
  public function getPendingChangeObjectInformation(
    StudyArea $studyArea,
    ReviewableInterface $object,
    ?PendingChange $exclude = null): PendingChangeObjectInfo
  {
    if (!$this->isReviewModeEnabledForObject($studyArea, $object)) {
      return new PendingChangeObjectInfo();
    }

    return new PendingChangeObjectInfo($this->pendingChangeRepository->getForObject($object, $exclude));
  }

  /** Retrieve whether the object can be edited. */
  public function canObjectBeEdited(StudyArea $studyArea, ReviewableInterface $object): bool
  {
    if (!$this->isReviewModeEnabledForObject($studyArea, $object)) {
      return true;
    }

    return 0 === count(array_filter($this->pendingChangeRepository->getForObject($object),
      static fn (PendingChange $pendingChange) => $pendingChange->getChangeType() !== PendingChange::CHANGE_TYPE_EDIT));
  }

  /** Retrieve whether the object can be removed. */
  public function canObjectBeRemoved(StudyArea $studyArea, ReviewableInterface $object): bool
  {
    if (!$this->isReviewModeEnabledForObject($studyArea, $object)) {
      return true;
    }

    return 0 === count($this->pendingChangeRepository->getForObject($object));
  }

  /**
   * Create a review from the supplied pending change context.
   * If requested, it will split existing pending changes into multiple ones.
   *
   * The exceptions can be thrown, but are unlikely. We do not want these exceptions to propagate to every controller.
   *
   * @noinspection PhpDocMissingThrowsInspection
   * @noinspection PhpUnhandledExceptionInspection
   */
  public function createReview(StudyArea $studyArea, array $markedChanges, User $reviewer, ?string $notes): void
  {
    /** @var PendingChange[] $pendingChanges */
    $pendingChanges = [];
    foreach ($this->pendingChangeRepository->getMultiple(array_keys($markedChanges)) as $pendingChange) {
      $pendingChanges[$pendingChange->getId()] = $pendingChange;
    }

    // Create the review
    $review = new Review()
      ->setOwner($this->getUser())
      ->setNotes($notes)
      ->setStudyArea($studyArea)
      ->setRequestedReviewAt(new DateTime())
      ->setRequestedReviewBy($reviewer);

    // Add the changes to the review
    foreach ($markedChanges as $pendingChangeId => $markedFields) {
      if (!array_key_exists($pendingChangeId, $pendingChanges)) {
        // Silently skip pending changes that no longer exist
        continue;
      }

      $pendingChange = $pendingChanges[$pendingChangeId];

      // Only split changes in case of edit
      if ($pendingChange->getChangeType() === PendingChange::CHANGE_TYPE_EDIT) {
        $fieldDiff = array_diff($pendingChange->getChangedFields(), $markedFields);
        if (0 !== count($fieldDiff)) {
          // Create a new pending change, but with the fields that were not selected at this time
          $newPendingChange = $pendingChange->duplicate(array_values($fieldDiff));
          $this->entityManager->persist($newPendingChange);

          // Update the existing pending change to only use the marked fields
          $pendingChange->setChangedFields($markedFields);
        }
      }

      // Add the pending change to the review
      $review->addPendingChange($pendingChange);
    }

    // Validate the entity
    if (count($violations = $this->validator->validate($review))) {
      assert($violations instanceof ConstraintViolationList);
      throw new InvalidArgumentException(sprintf('Pending change validation not passed! %s', $violations));
    }

    // Save the review
    $this->entityManager->persist($review);
    $this->entityManager->flush();

    // Send notification
    $this->reviewNotificationService->reviewRequested($review);
  }

  /**
   * Publish the review.
   *
   * @throws ORMException
   * @throws Throwable
   */
  public function publishReview(Review $review): void
  {
    // Loop the changes to apply them
    foreach ($review->getPendingChanges() as $pendingChange) {
      $this->applyChange($pendingChange);
    }

    // Remove the review now
    $this->entityManager->remove($review);

    // Flush the changes in a transaction
    $this->entityManager->wrapInTransaction(static function (EntityManagerInterface $em) {
      $em->flush();
    });

    $this->reviewNotificationService->submissionPublished($review);
  }

  /** Retrieve the data snapshot used for change detection. */
  public static function getDataSnapshot(ReviewableInterface $object): string
  {
    return self::getSerializer()->serialize($object, self::SERIALIZER_FORMAT, self::getSerializationContext());
  }

  /** Retrieve the data object from a change snapshot. */
  public static function getObjectFromSnapshot(string $snapshot, string $objectType): ReviewableInterface
  {
    $object = self::getSerializer()->deserialize($snapshot, $objectType, self::SERIALIZER_FORMAT);
    assert($object instanceof ReviewableInterface);

    return $object;
  }

  /** Retrieve the serializer used for the change serialization. */
  public static function getSerializer(): SerializerInterface
  {
    if (!self::$serializer) {
      $serializerBuilder = SerializerBuilder::create()
        ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()));
      self::$serializer  = $serializerBuilder->build();
    }

    return self::$serializer;
  }

  /** Retrieve the serialization context for the change serialization. */
  public static function getSerializationContext(): SerializationContext
  {
    return SerializationContext::create()
      ->setSerializeNull(true)
      ->enableMaxDepthChecks()
      ->setGroups([
        'review_change',
        'elements' => [
          'review_change',
          'concept' => ['id_only', 'name_only'],
          'next'    => ['id_only'],
        ],
        'outgoingRelations' => [
          'review_change',
          'source'       => ['id_only', 'name_only'],
          'target'       => ['id_only', 'name_only'],
          'relationType' => ['id_only', 'name_only'],
        ],
        'incomingRelations' => [
          'review_change',
          'source'       => ['id_only', 'name_only'],
          'target'       => ['id_only', 'name_only'],
          'relationType' => ['id_only', 'name_only'],
        ],
      ]);
  }

  /**
   * Applies the pending change.
   *
   * @throws EntityNotFoundException
   * @throws ORMException
   * @throws InvalidChangeException
   */
  private function applyChange(PendingChange $pendingChange): void
  {
    $changeType = $pendingChange->getChangeType();
    $objectType = $pendingChange->getObjectType();

    if ($changeType === PendingChange::CHANGE_TYPE_ADD) {
      // Create a new instance of the object
      assert(is_string($objectType) && $objectType !== '');
      $object = new $objectType();
      assert($object instanceof ReviewableInterface);
      $object->setStudyArea($pendingChange->getStudyArea());

      // Set the updated fields in it
      $object->applyChanges($pendingChange, $this->entityManager);

      // Persist it
      $this->entityManager->persist($object);
    } elseif ($changeType === PendingChange::CHANGE_TYPE_EDIT || $changeType === PendingChange::CHANGE_TYPE_REMOVE) {
      $object = $this->getOriginalObject($pendingChange);

      if ($changeType === PendingChange::CHANGE_TYPE_EDIT) {
        // Apply the changes
        $object->applyChanges($pendingChange, $this->entityManager);
      } else {
        // Remove the object
        $this->entityManager->remove($object);

        // Return directly, validation does not apply in this case
        return;
      }
    } else {
      throw new InvalidArgumentException(sprintf('Change type "%s" is not supported', $changeType));
    }

    // Validate the new/updated entity
    if (0 !== count($violations = $this->validator->validate($object))) {
      assert($violations instanceof ConstraintViolationList);
      throw new InvalidArgumentException(sprintf('Validation not passed during publish! %s', $violations));
    }
  }

  /** Determines the changed fields based on the snapshot. */
  private function determineChangedFieldsFromSnapshot(ReviewableInterface $object, string $originalSnapshot): array
  {
    if (null === $originalSnapshot) {
      throw new InvalidArgumentException('Snapshot must be given!');
    }

    $changedFields = [];

    // Create a snapshot of the new data
    $newSnapshot = self::getDataSnapshot($object);

    // Deserialize the diff the properties
    $newSnapshotArray      = json_decode($newSnapshot, true);
    $originalSnapshotArray = json_decode($originalSnapshot, true);

    // Compare the data
    foreach ($newSnapshotArray as $key => $data) {
      $origData = array_key_exists($key, $originalSnapshotArray) ? $originalSnapshotArray[$key] : null;

      // The relation field are rebuild every time, so we need to exclude the id property from this test
      if ($object->getReviewName() === Concept::class && ($key === 'relations' || $key === 'incomingRelations')) {
        foreach ($origData as &$relation) {
          unset($relation['id']);
        }
        foreach ($data as &$relation) {
          unset($relation['id']);
        }
      }

      if ($this->asSimpleType($data) !== $this->asSimpleType($origData)) {
        $changedFields[] = $key;
      }
    }

    return $changedFields;
  }

  /** Convert value to simple type which can be compared by simple if statements. */
  private function asSimpleType(&$value): string|false|null
  {
    if ($value === null) {
      return null;
    }

    if (is_string($value) || is_numeric($value)) {
      return $value;
    }

    return json_encode($value);
  }

  /** Adds a flash message to the current session for type. */
  private function addFlash(string $type, string $message): void
  {
    if (!$request = $this->requestStack->getMainRequest()) {
      $session = $request->getSession();
      assert($session instanceof Session);
      $session->getFlashBag()->add($type, $message);
    }
  }

  /**
   * Used when the requested change does not require review.
   *
   * @throws ORMException
   * @throws OptimisticLockException
   */
  private function directSave(ReviewableInterface $object, string $changeType, ?callable $directCallback = null): void
  {
    if ($directCallback) {
      $directCallback($object);
    }

    if ($changeType === PendingChange::CHANGE_TYPE_REMOVE && !$object instanceof RelationType) {
      $this->entityManager->remove($object);
    } elseif ($changeType === PendingChange::CHANGE_TYPE_ADD) {
      $this->entityManager->persist($object);
    }

    $this->entityManager->flush();
  }

  /** Get the current user. */
  private function getUser(): User
  {
    $user = $this->security->getUser();
    assert($user instanceof User);

    return $user;
  }
}
