<?php

namespace App\DuplicationUtils;

use App\Entity\Abbreviation;
use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\ExternalResource;
use App\Entity\LearningOutcome;
use App\Entity\RelationType;
use App\Entity\StudyArea;
use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRelationRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class StudyAreaDuplicator
{
  /** @var string */
  private $uploadsPath;

  /** @var EntityManagerInterface */
  private $em;

  /** @var AbbreviationRepository */
  private $abbreviationRepo;

  /** @var ConceptRelationRepository */
  private $conceptRelationRepo;

  /** @var ExternalResourceRepository */
  private $externalResourceRepo;

  /** @var LearningOutcomeRepository */
  private $learningOutcomeRepo;

  /** @var StudyArea */
  private $studyAreaToDuplicate;

  /** @var StudyArea */
  private $newStudyArea;

  /** @var Concept[] */
  private $concepts;

  /**
   * StudyAreaDuplicator constructor.
   *
   * @param string                     $projectDir
   * @param EntityManagerInterface     $em
   * @param AbbreviationRepository     $abbreviationRepo
   * @param ConceptRelationRepository  $conceptRelationRepo
   * @param ExternalResourceRepository $externalResourceRepo
   * @param LearningOutcomeRepository  $learningOutcomeRepo
   *
   * @param StudyArea                  $studyAreaToDuplicate Study area to duplicate
   * @param StudyArea                  $newStudyArea         New study area
   * @param Concept[]                  $concepts             Concepts to copy
   */
  public function __construct(string $projectDir, EntityManagerInterface $em, AbbreviationRepository $abbreviationRepo,
                              ConceptRelationRepository $conceptRelationRepo, ExternalResourceRepository $externalResourceRepo,
                              LearningOutcomeRepository $learningOutcomeRepo, StudyArea $studyAreaToDuplicate,
                              StudyArea $newStudyArea, array $concepts)
  {
    $this->uploadsPath          = $projectDir . '/public/uploads/studyarea';
    $this->em                   = $em;
    $this->abbreviationRepo     = $abbreviationRepo;
    $this->conceptRelationRepo  = $conceptRelationRepo;
    $this->externalResourceRepo = $externalResourceRepo;
    $this->learningOutcomeRepo  = $learningOutcomeRepo;
    $this->studyAreaToDuplicate = $studyAreaToDuplicate;
    $this->newStudyArea         = $newStudyArea;
    $this->concepts             = $concepts;
  }

  /**
   * Duplicates the given study area into the new study area
   *
   * @throws \Exception
   */
  public function duplicate()
  {
    $this->em->getConnection()->beginTransaction();
    try {
      // Persist the new study area, and flush to retrieve id
      $this->em->persist($this->newStudyArea);
      $this->em->flush();

      // Duplicate the study area learning outcomes
      $newLearningOutcomes = $this->duplicateLearningOutcomes();

      // Duplicate the study area external resources
      $newExternalResources = $this->duplicateExternalResources();

      // Duplicate the study area abbreviations
      $this->duplicateAbbreviations();

      // Duplicate the concepts
      $newConcepts = $this->duplicateConcepts($newLearningOutcomes, $newExternalResources);

      // Duplicate the relations and relation types for the study area
      $this->duplicateRelations($newConcepts);

      // Duplicate the uploads
      $this->duplicateUploads();

      // Save the data
      $this->em->flush();

      $this->em->getConnection()->commit();
    } catch (\Exception $e) {
      $this->removeUploads();
      $this->em->getConnection()->rollBack();
      throw $e;
    }
  }

  /**
   * @return LearningOutcome[]
   */
  private function duplicateLearningOutcomes(): array
  {
    $newLearningOutcomes = [];
    $learningOutcomes    = $this->learningOutcomeRepo->findForConcepts($this->concepts);
    foreach ($learningOutcomes as $learningOutcome) {
      $newLearningOutcome = (new LearningOutcome())
          ->setStudyArea($this->newStudyArea)
          ->setNumber($learningOutcome->getNumber())
          ->setName($learningOutcome->getName())
          ->setText($this->updateUrls($learningOutcome->getText()));

      $this->em->persist($newLearningOutcome);
      $newLearningOutcomes[$learningOutcome->getId()] = $newLearningOutcome;
    }

    return $newLearningOutcomes;
  }

  /**
   * Duplicate the external resources
   *
   * @return ExternalResource[]
   */
  private function duplicateExternalResources(): array
  {
    $externalResources    = $this->externalResourceRepo->findForConcepts($this->concepts);
    $newExternalResources = [];
    foreach ($externalResources as $externalResource) {
      $newExternalResource = (new ExternalResource())
          ->setStudyArea($this->newStudyArea)
          ->setTitle($externalResource->getTitle())
          ->setDescription($this->updateUrls($externalResource->getDescription()))
          ->setUrl($this->updateUrls($externalResource->getUrl()))
          ->setBroken($externalResource->isBroken());

      $this->em->persist($newExternalResource);
      $newExternalResources[$externalResource->getId()] = $newExternalResource;
    }

    return $newExternalResources;
  }


  /**
   * Duplicate the abbreviations
   */
  private function duplicateAbbreviations(): void
  {
    $abbreviations = $this->abbreviationRepo->findForStudyArea($this->studyAreaToDuplicate);
    foreach ($abbreviations as $abbreviation) {
      $newAbbreviation = (new Abbreviation())
          ->setStudyArea($this->newStudyArea)
          ->setAbbreviation($abbreviation->getAbbreviation())
          ->setMeaning($abbreviation->getMeaning());

      $this->em->persist($newAbbreviation);
    }
  }

  /**
   * Duplicate the concepts
   *
   * @param $newLearningOutcomes
   * @param $newExternalResources
   *
   * @return Concept[]
   */
  private function duplicateConcepts($newLearningOutcomes, $newExternalResources): array
  {
    /** @var Concept[] $newConcepts */
    $newConcepts     = [];
    $priorKnowledges = [];
    foreach ($this->concepts as $concept) {
      $newConcept = new Concept();
      $newConcept
          ->setName($concept->getName())
          ->setIntroduction($newConcept->getIntroduction()->setText(
              $this->updateUrls($concept->getIntroduction()->getText())))
          ->setSynonyms($concept->getSynonyms())
          ->setTheoryExplanation($newConcept->getTheoryExplanation()->setText(
              $this->updateUrls($concept->getTheoryExplanation()->getText())))
          ->setHowTo($newConcept->getHowTo()->setText(
              $this->updateUrls($concept->getHowTo()->getText())))
          ->setExamples($newConcept->getExamples()->setText(
              $this->updateUrls($concept->getExamples()->getText())))
          ->setSelfAssessment($newConcept->getSelfAssessment()->setText(
              $this->updateUrls($concept->getSelfAssessment()->getText())))
          ->setStudyArea($this->newStudyArea);

      // Set learning outcomes
      foreach ($concept->getLearningOutcomes() as $oldLearningOutcome) {
        $newConcept->addLearningOutcome($newLearningOutcomes[$oldLearningOutcome->getId()]);
      }

      // Set external resources
      foreach ($concept->getExternalResources() as $oldExternalResource) {
        $newConcept->addExternalResource($newExternalResources[$oldExternalResource->getId()]);
      }

      // Save current prior knowledge to update them later when the concept map is complete
      $priorKnowledges[$concept->getId()] = $concept->getPriorKnowledge();

      $newConcepts[$concept->getId()] = $newConcept;
      $this->em->persist($newConcept);
    }

    // Loop the concepts again to add the prior knowledge
    foreach ($newConcepts as $oldId => &$newConcept) {
      foreach ($priorKnowledges[$oldId] as $priorKnowledge) {
        /** @var Concept $priorKnowledge */
        if (array_key_exists($priorKnowledge->getId(), $newConcepts)) {
          $newConcept->addPriorKnowledge($newConcepts[$priorKnowledge->getId()]);
        }
      }
    }

    return $newConcepts;
  }

  /**
   * @param $newConcepts
   */
  private function duplicateRelations($newConcepts): void
  {
    $conceptRelations    = $this->conceptRelationRepo->findByConcepts($this->concepts);
    $newRelationTypes    = [];
    $newConceptRelations = [];
    foreach ($conceptRelations as $conceptRelation) {
      // Skip relation for concepts that aren't duplicated
      if (!array_key_exists($conceptRelation->getSource()->getId(), $newConcepts)
          || !array_key_exists($conceptRelation->getTarget()->getId(), $newConcepts)) {
        continue;
      }

      $relationType = $conceptRelation->getRelationType();

      // Duplicate relation type, if not done yet
      if (!array_key_exists($relationType->getId(), $newRelationTypes)) {
        $newRelationType = (new RelationType())
            ->setStudyArea($this->newStudyArea)
            ->setName($relationType->getName());

        $newRelationTypes[$relationType->getId()] = $newRelationType;
        $this->em->persist($newRelationType);
      }

      // Duplicate relation
      $newConceptRelation = (new ConceptRelation())
          ->setSource($newConcepts[$conceptRelation->getSource()->getId()])
          ->setTarget($newConcepts[$conceptRelation->getTarget()->getId()])
          ->setRelationType($newRelationTypes[$relationType->getId()])
          ->setIncomingPosition($conceptRelation->getIncomingPosition())
          ->setOutgoingPosition($conceptRelation->getOutgoingPosition());

      $newConceptRelations[] = $conceptRelation;
      $this->em->persist($newConceptRelation);
    }
  }

  /**
   * Duplicates the uploads directory, if any
   */
  private function duplicateUploads(): void
  {
    $fileSystem = new Filesystem();
    $source     = $this->getStudyAreaDirectory($this->studyAreaToDuplicate);

    if (!$fileSystem->exists($source)) {
      // Nothing to duplicate
      return;
    }

    $fileSystem->mirror($source, $this->getStudyAreaDirectory($this->newStudyArea));
  }

  /**
   * Removes the uploads directory, if any
   */
  private function removeUploads(): void
  {
    $fileSystem = new Filesystem();
    $directory  = $this->getStudyAreaDirectory($this->newStudyArea);
    if (!$fileSystem->exists($directory)) {
      // Nothing to remove
      return;
    }

    $fileSystem->remove($directory);
  }

  /**
   * Retrieve the study area uploads path
   *
   * @param StudyArea $studyArea
   *
   * @return string
   */
  private function getStudyAreaDirectory(StudyArea $studyArea)
  {
    // Check for null
    if ($studyArea->getId() === NULL) {
      throw new \InvalidArgumentException('Study area id is NULL!');
    }

    return sprintf('%s/%d', $this->uploadsPath, $studyArea->getId());
  }

  /**
   * Replaces study area urls with the id of the area to duplicate with the id of the new study area
   *
   * @param string $text
   *
   * @return string
   */
  private function updateUrls(string $text): string
  {
    return $text;
  }
}
