<?php

namespace App\Review;

use App\Entity\Contracts\ReviewableInterface;
use App\Entity\PendingChange;
use App\Entity\StudyArea;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use InvalidArgumentException;
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
            ? $this->determineChanged($object) : []);

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
  private function determineChanged(ReviewableInterface $object): array
  {
    $classMetadata    = $this->entityManager->getClassMetadata(get_class($object));
    $unitOfWork       = $this->entityManager->getUnitOfWork();
    $reviewFieldNames = $object->getReviewFieldsNames();

    // If new, return all fields
    if ($unitOfWork->getEntityState($object) === UnitOfWork::STATE_NEW) {
      return $reviewFieldNames;
    }

    $fieldNames     = array_intersect($classMetadata->getFieldNames(), $reviewFieldNames);
    $originalValues = $unitOfWork->getOriginalEntityData($object);
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

    // Loop the relations to detect changes
    $associationFieldNames = array_intersect($classMetadata->getAssociationNames(), $reviewFieldNames);
    foreach ($associationFieldNames as $associationFieldName) {
      // Retrieve value and check whether it is reviewable
      $associationObject = $classMetadata->getFieldValue($object, $associationFieldName);
      if (!$associationObject instanceof ReviewableInterface) {
        continue;
      }

      // Validate original values
      $associationClassMetadata    = $this->entityManager->getClassMetadata(get_class($associationObject));
      $associationOriginalValue    = $unitOfWork->getOriginalEntityData($associationObject);
      $associationObjectFieldNames = array_intersect($associationClassMetadata->getFieldNames(), $associationObject->getReviewFieldsNames());

      foreach ($associationObjectFieldNames as $associationObjectFieldName) {
        // Validate value
        if ($associationOriginalValue[$associationObjectFieldName] !== $associationClassMetadata->getFieldValue($associationObject, $associationObjectFieldName)) {
          $changedFields[] = $associationFieldName;

          // Reset original value to prevent side effects
          $associationClassMetadata->setFieldValue($associationObject, $associationObjectFieldName, $associationOriginalValue[$associationObjectFieldName]);
        }
      }
    }

    return $changedFields;
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
