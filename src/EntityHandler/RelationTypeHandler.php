<?php

namespace App\EntityHandler;

use App\Entity\PendingChange;
use App\Entity\RelationType;
use DateTime;
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

class RelationTypeHandler extends AbstractEntityHandler
{
  public function add(RelationType $relationType, string $snapshot = null): void
  {
    $this->validate($relationType);

    if ($this->useReviewService($snapshot)) {
      $this->reviewService->storeChange(
          $relationType->getStudyArea(), $relationType, PendingChange::CHANGE_TYPE_ADD, $snapshot);
    } else {
      $this->em->persist($relationType);
      $this->em->flush();
    }
  }

  public function update(RelationType $relationType, string $snapshot = null): void
  {
    if ($relationType->getDeletedAt() !== null) {
      throw new InvalidArgumentException('Cannot update deleted relation type!');
    }

    $this->validate($relationType);

    if ($this->useReviewService($snapshot)) {
      $this->reviewService->storeChange(
          $relationType->getStudyArea(), $relationType, PendingChange::CHANGE_TYPE_EDIT, $snapshot);
    } else {
      $this->em->flush();
    }
  }

  public function delete(RelationType $relationType, mixed $user): void
  {
    if ($relationType->getDeletedAt() !== null) {
      return;
    }

    // Remove the relation type by setting the deletedAt/By manually
    $removeFunction = fn () => $relationType
        ->setDeletedAt(new DateTime())
        ->setDeletedBy($user instanceof UserInterface ? $user->getUsername() : 'anon.');

    if ($this->reviewService !== null) {
      // This must be registered as remove change, but it must be handled differently when actually removed
      $this->reviewService->storeChange(
          $relationType->getStudyArea(), $relationType, PendingChange::CHANGE_TYPE_REMOVE, null, $removeFunction);
    } else {
      $removeFunction();
      $this->em->flush();
    }
  }
}
