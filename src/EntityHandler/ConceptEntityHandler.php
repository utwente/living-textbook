<?php

namespace App\EntityHandler;

use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\PendingChange;
use App\Repository\LearningPathRepository;
use Doctrine\Common\Collections\ArrayCollection;

class ConceptEntityHandler extends AbstractEntityHandler
{
  public function add(Concept $concept, string $snapshot = null): void
  {
    $this->validate($concept);

    if ($this->useReviewService($snapshot)) {
      $this->reviewService->storeChange(
          $concept->getStudyArea(), $concept, PendingChange::CHANGE_TYPE_ADD, $snapshot);
    } else {
      $this->em->persist($concept);
      $this->em->flush();
    }
  }

  public function addRelation(ConceptRelation $relation): void
  {
    $this->validate($relation);

    $this->update($relation->getSource()->addOutgoingRelation($relation));
  }

  public function update(
      Concept $concept,
      string $snapshot = null,
      ArrayCollection $originalOutgoingRelations = null,
      ArrayCollection $originalIncomingRelations = null): void
  {
    $this->validate($concept);

    $updateFunction = function () use (&$originalIncomingRelations, &$originalOutgoingRelations) {
      // Remove outdated relations
      if ($originalOutgoingRelations !== null) {
        foreach ($originalOutgoingRelations as $originalOutgoingRelation) {
          // Remove all original relations, because we just make new ones
          $this->em->remove($originalOutgoingRelation);
        }
      }
      if ($originalIncomingRelations !== null) {
        foreach ($originalIncomingRelations as $originalIncomingRelation) {
          // Remove all original relations, because we just make new ones
          $this->em->remove($originalIncomingRelation);
        }
      }
    };

    if ($this->useReviewService($snapshot)) {
      $this->reviewService->storeChange(
          $concept->getStudyArea(), $concept, PendingChange::CHANGE_TYPE_EDIT, $snapshot, $updateFunction);
    } else {
      $updateFunction();
      $this->em->flush();
    }
  }

  public function updateRelation(ConceptRelation $relation, string $snapshot = null): void
  {
    $this->validate($relation);
    $this->em->flush();
  }

  /** @noinspection PhpUnhandledExceptionInspection */
  public function delete(Concept $concept, LearningPathRepository $learningPathRepository): void
  {
    $deleteFunction = fn () => $learningPathRepository->removeElementBasedOnConcept($concept);

    if ($this->reviewService !== null) {
      $this->reviewService->storeChange(
          $concept->getStudyArea(), $concept, PendingChange::CHANGE_TYPE_REMOVE, null, $deleteFunction);
    } else {
      $deleteFunction();
      $this->em->remove($concept);
      $this->em->flush();
    }
  }

  public function deleteRelation(ConceptRelation $conceptRelation): void
  {
    $this->em->remove($conceptRelation);
    $this->em->flush();
  }
}
