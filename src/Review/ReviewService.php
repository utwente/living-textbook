<?php

namespace App\Review;

use App\Entity\Contracts\ReviewableInterface;
use App\Entity\PendingChange;
use App\Entity\StudyArea;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\UnitOfWork;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReviewService
{

  /**
   * @var EntityManagerInterface
   */
  private $entityManager;
  /**
   * @var UnitOfWork
   */
  private $unitOfWork;
  /**
   * @var Session
   */
  private $session;
  /**
   * @var TranslatorInterface
   */
  private $translator;
  /**
   * @var ValidatorInterface
   */
  private $validator;

  public function __construct(
      EntityManagerInterface $entityManager, ValidatorInterface $validator, SessionInterface $session,
      TranslatorInterface $translator)
  {
    $this->entityManager = $entityManager;
    $this->unitOfWork    = $entityManager->getUnitOfWork();
    $this->validator     = $validator;
    $this->session       = $session;
    $this->translator    = $translator;
  }

  /**
   * @param StudyArea           $studyArea
   * @param ReviewableInterface $object
   * @param string              $changeType
   * @param callable|null       $directCallback
   */
  public function storeChange(StudyArea $studyArea, ReviewableInterface $object, string $changeType, callable $directCallback = NULL)
  {
    if (!in_array($changeType, PendingChange::CHANGE_TYPES)) {
      throw new InvalidArgumentException(sprintf("Supplied change type %s is not valid", $changeType));
    }

    // Check for review mode: when not enabled, do the direct save
    if (!$studyArea->isReviewModeEnabled()) {
      $this->directSave($object, $changeType, $directCallback);

      return;
    }

    // Create the pending change entity
    $change = (new PendingChange())
        ->setStudyArea($studyArea)
        ->setChangeType($changeType)
        ->setObject($object)
        ->setObjectId($object->getId())
        ->setObjectType($object->getReviewName())
        ->setChangedFields($changeType !== PendingChange::CHANGE_TYPE_REMOVE
            ? $this->determineChangedFields($object) : []);

    // If nothing has changed, we have nothing to do for review and we use the original behavior
    if ($changeType !== PendingChange::CHANGE_TYPE_REMOVE && 0 === count($change->getChangedFields())) {
      // Use the normal save behavior
      $this->directSave($object, $changeType, $directCallback);

      return;
    }

    // Validate the entity
    if (count($violations = $this->validator->validate($change))) {
      assert($violations instanceof ConstraintViolationList);
      throw new InvalidArgumentException(sprintf('Pending change validation not passed! %s', $violations));
    }

    // Store the pending change
    $this->entityManager->persist($change);
    $this->entityManager->flush();

    // Add flash notification about the review change
    $this->addFlash('notice', $this->translator->trans('review.saved-for-review'));
  }

  /**
   * Determines the changed fields
   *
   * @param ReviewableInterface $object
   *
   * @return array
   */
  private function determineChangedFields(ReviewableInterface $object): array
  {
    $classMetadata    = $this->entityManager->getClassMetadata(get_class($object));
    $reviewFieldNames = $object->getReviewFieldNames();

    // If new, return all fields
    if ($this->unitOfWork->getEntityState($object) === UnitOfWork::STATE_NEW) {
      return $reviewFieldNames;
    }

    $fieldNames     = array_intersect($classMetadata->getFieldNames(), $reviewFieldNames);
    $originalValues = $this->unitOfWork->getOriginalEntityData($object);
    $changedFields  = [];

    // Loop the fields to detect the changes
    foreach ($fieldNames as $fieldName) {
      // Validate value
      if ($originalValues[$fieldName] !== $classMetadata->getFieldValue($object, $fieldName)) {
        $changedFields[] = $fieldName;

        // Reset original value to prevent side effects
        $classMetadata->setFieldValue($object, $fieldName, $originalValues[$fieldName]);
      }
    }

    // Loop the one to one relations to detect changes in the related objects
    // This only works for one-to-one relations, as we can retrieve the original data from it and restore it!
    $associationFieldNames = array_intersect($classMetadata->getAssociationNames(), $reviewFieldNames);
    foreach ($associationFieldNames as $associationFieldName) {
      if (!$classMetadata->isSingleValuedAssociation($associationFieldName)) {
        throw new RuntimeException('Collection associations are not supported by only specifying a field name!');
      }

      /** @noinspection PhpUnhandledExceptionInspection Cannot happen here */
      $associationMapping = $classMetadata->getAssociationMapping($associationFieldName);
      if ($associationMapping['type'] !== ClassMetadata::ONE_TO_ONE) {
        throw new RuntimeException('This tool can one use OneToOne associations or simple fields names as review field name!');
      }

      // Retrieve value and check whether it is reviewable
      $associationObject = $classMetadata->getFieldValue($object, $associationFieldName);
      if (!$associationObject instanceof ReviewableInterface) {
        throw new RuntimeException(sprintf('The one-to-one relation must implement %s!', ReviewableInterface::class));
      }

      // This is a single
      if ($this->determineChangesInAssociation($associationObject)) {
        $changedFields[] = $associationFieldName;
      }
    }

    // Review the id linked relations
    // It is unknown if this works, as there is currently no entity using this
    $associationFieldNames = array_intersect($classMetadata->getAssociationNames(), $object->getReviewIdFieldNames());
    foreach ($associationFieldNames as $associationFieldName) {
      if (!$classMetadata->isSingleValuedAssociation($associationFieldName)) {
        throw new RuntimeException('Collection associations are not supported by only specifying a association field name!');
      }

      /** @noinspection PhpUnhandledExceptionInspection Cannot happen here */
      $associationMapping = $classMetadata->getAssociationMapping($associationFieldName);
      if ($associationMapping['type'] !== ClassMetadata::MANY_TO_ONE) {
        throw new RuntimeException('This tool can one use ManyToOne associations as review id field name!');
      }

      // Retrieve value
      $associationObject = $classMetadata->getFieldValue($object, $associationFieldName);
      $originalValueKey  = $associationFieldName . '_id';
      if (!array_key_exists($associationFieldName, $originalValues)) {
        throw new RuntimeException('The id value cannot be determined from the original data!');
      }

      if ($originalValues[$originalValueKey] !== $associationObject->getId()) {
        $changedFields[] = $associationFieldName;

        try {
          $classMetadata->setFieldValue($object, $associationFieldName,
              $this->entityManager->getReference($associationMapping['targetEntity'], $originalValues[$originalValueKey]));
        } catch (ORMException $e) {
          throw new RuntimeException(sprintf('Could not restore the original ManyToOne value for field %s.', $associationFieldName), NULL, $e);
        }
      }
    }

    return array_unique($changedFields);
  }

  /**
   * Determine whether the association is changed
   *
   * @param ReviewableInterface $associationObject
   *
   * @return bool
   */
  private function determineChangesInAssociation(ReviewableInterface $associationObject): bool
  {
    // Validate original values
    $associationClassMetadata    = $this->entityManager->getClassMetadata(get_class($associationObject));
    $associationOriginalValue    = $this->unitOfWork->getOriginalEntityData($associationObject);
    $associationObjectFieldNames = array_intersect($associationClassMetadata->getFieldNames(), $associationObject->getReviewFieldNames());

    $changed = false;
    foreach ($associationObjectFieldNames as $associationObjectFieldName) {
      // Validate value
      if ($associationOriginalValue[$associationObjectFieldName] !== $associationClassMetadata->getFieldValue($associationObject, $associationObjectFieldName)) {
        $changed = true;

        // Reset original value to prevent side effects
        $associationClassMetadata->setFieldValue($associationObject, $associationObjectFieldName, $associationOriginalValue[$associationObjectFieldName]);
      }
    }

    return $changed;
  }

  /**
   * Adds a flash message to the current session for type.
   *
   * @param string $type
   * @param string $message
   */
  private function addFlash(string $type, string $message)
  {
    $this->session->getFlashBag()->add($type, $message);
  }

  /**
   * Used when the requested change does not require review
   *
   * @param ReviewableInterface $object
   * @param string              $changeType
   * @param callable|NULL       $directCallback
   */
  private function directSave(ReviewableInterface $object, string $changeType, callable $directCallback = NULL)
  {
    if ($directCallback) {
      $directCallback($object);
    }

    if ($changeType === PendingChange::CHANGE_TYPE_REMOVE) {
      $this->entityManager->remove($object);
    } else if ($changeType === PendingChange::CHANGE_TYPE_ADD) {
      $this->entityManager->persist($object);
    }

    $this->entityManager->flush();
  }
}
