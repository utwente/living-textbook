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

  /**
   * StudyAreaDuplicator constructor.
   *
   * @param string                     $projectDir
   * @param EntityManagerInterface     $em
   * @param AbbreviationRepository     $abbreviationRepo
   * @param ConceptRelationRepository  $conceptRelationRepo
   * @param ExternalResourceRepository $externalResourceRepo
   * @param LearningOutcomeRepository  $learningOutcomeRepo
   */
  public function __construct(string $projectDir, EntityManagerInterface $em, AbbreviationRepository $abbreviationRepo,
                              ConceptRelationRepository $conceptRelationRepo, ExternalResourceRepository $externalResourceRepo,
                              LearningOutcomeRepository $learningOutcomeRepo)
  {
    $this->uploadsPath          = $projectDir . '/public/uploads/studyarea';
    $this->em                   = $em;
    $this->abbreviationRepo     = $abbreviationRepo;
    $this->conceptRelationRepo  = $conceptRelationRepo;
    $this->externalResourceRepo = $externalResourceRepo;
    $this->learningOutcomeRepo  = $learningOutcomeRepo;
  }

  /**
   * Duplicates the given study area into the new study area
   *
   * @param StudyArea $studyAreaToDuplicate Study area to duplicate
   * @param StudyArea $newStudyArea         New study area
   * @param Concept[] $concepts             Concepts to copy
   *
   * @throws \Exception
   */
  public function duplicate(StudyArea $studyAreaToDuplicate, StudyArea $newStudyArea, array $concepts)
  {
    $this->em->getConnection()->beginTransaction();
    try {
      // Persist the new study area, and flush to retrieve id
      $this->em->persist($newStudyArea);
      $this->em->flush();

      // Duplicate the study area learning outcomes
      $learningOutcomes = $this->learningOutcomeRepo->findForConcepts($concepts);;
      $newLearningOutcomes = [];
      foreach ($learningOutcomes as $learningOutcome) {
        $newLearningOutcome = (new LearningOutcome())
            ->setStudyArea($newStudyArea)
            ->setNumber($learningOutcome->getNumber())
            ->setName($learningOutcome->getName())
            ->setText($learningOutcome->getText());

        $this->em->persist($newLearningOutcome);
        $newLearningOutcomes[$learningOutcome->getId()] = $newLearningOutcome;
      }

      // Duplicate the study area external resources
      $externalResources = $this->externalResourceRepo->findForConcepts($concepts);;
      $newExternalResources = [];
      foreach ($externalResources as $externalResource) {
        $newExternalResource = (new ExternalResource())
            ->setStudyArea($newStudyArea)
            ->setTitle($externalResource->getTitle())
            ->setDescription($externalResource->getDescription())
            ->setUrl($externalResource->getUrl())
            ->setBroken($externalResource->isBroken());

        $this->em->persist($newExternalResource);
        $newExternalResources[$externalResource->getId()] = $newExternalResource;
      }

      // Duplicate the study area abbreviations
      $abbreviations = $this->abbreviationRepo->findForStudyArea($studyAreaToDuplicate);
      foreach ($abbreviations as $abbreviation) {
        $newAbbreviation = (new Abbreviation())
            ->setStudyArea($newStudyArea)
            ->setAbbreviation($abbreviation->getAbbreviation())
            ->setMeaning($abbreviation->getMeaning());

        $this->em->persist($newAbbreviation);
      }

      // Duplicate the concepts
      /** @var Concept[] $newConcepts */
      $newConcepts     = [];
      $priorKnowledges = [];
      foreach ($concepts as $concept) {
        $newConcept = new Concept();
        $newConcept
            ->setName($concept->getName())
            ->setIntroduction($newConcept->getIntroduction()->setText($concept->getIntroduction()->getText()))
            ->setSynonyms($concept->getSynonyms())
            ->setTheoryExplanation($newConcept->getTheoryExplanation()->setText($concept->getTheoryExplanation()->getText()))
            ->setHowTo($newConcept->getHowTo()->setText($concept->getHowTo()->getText()))
            ->setExamples($newConcept->getExamples()->setText($concept->getExamples()->getText()))
            ->setSelfAssessment($newConcept->getSelfAssessment()->setText($concept->getSelfAssessment()->getText()))
            ->setStudyArea($newStudyArea);

        // Set learning outcomes
        foreach ($concept->getLearningOutcomes() as $learningOutcome) {
          $newConcept->addLearningOutcome($newLearningOutcomes[$learningOutcome->getId()]);
        }

        // Set external resources
        foreach ($concept->getExternalResources() as $externalResource) {
          $newConcept->addExternalResource($newExternalResources[$externalResource->getId()]);
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

      // Duplicate the relations and relation types for the study area
      $conceptRelations    = $this->conceptRelationRepo->findByConcepts($concepts);
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
              ->setStudyArea($newStudyArea)
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

      // Duplicate the uploads
      $this->duplicateUploads($studyAreaToDuplicate, $newStudyArea);

      // Save the data
      $this->em->flush();

      $this->em->getConnection()->commit();
    } catch (\Exception $e) {
      $this->removeUploads($newStudyArea);
      $this->em->getConnection()->rollBack();
      throw $e;
    }
  }

  /**
   * Duplicates the uploads directory, if any
   *
   * @param StudyArea $studyAreaToDuplicate
   * @param StudyArea $newStudyArea
   */
  private function duplicateUploads(StudyArea $studyAreaToDuplicate, StudyArea $newStudyArea)
  {
    $fileSystem = new Filesystem();
    $source     = $this->getStudyAreaDirectory($studyAreaToDuplicate);

    if (!$fileSystem->exists($source)) {
      // Nothing to duplicate
      return;
    }

    $fileSystem->mirror($source, $this->getStudyAreaDirectory($newStudyArea));
  }

  /**
   * Removes the uploads directory, if any
   *
   * @param StudyArea $newStudyArea
   */
  private function removeUploads(StudyArea $newStudyArea)
  {
    $fileSystem = new Filesystem();
    $directory  = $this->getStudyAreaDirectory($newStudyArea);
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
    if ($studyArea->getId()) {
      throw new \InvalidArgumentException('Study area id is NULL!');
    }

    return sprintf('%s/%d', $this->uploadsPath, $studyArea->getId());
  }
}
