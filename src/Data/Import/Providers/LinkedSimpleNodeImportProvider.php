<?php

namespace App\Data\Import\Providers;

use App\Data\Import\ImportProviderInterface;
use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\Contributor;
use App\Entity\ExternalResource;
use App\Entity\LearningOutcome;
use App\Entity\RelationType;
use App\Entity\StudyArea;
use App\Entity\StudyAreaFieldConfiguration;
use App\Entity\Tag;
use App\Exception\DataImportException;
use App\Repository\LearningOutcomeRepository;
use App\Repository\RelationTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function array_key_exists;
use function is_array;
use function json_encode;
use function sprintf;

#[Lazy]
final readonly class LinkedSimpleNodeImportProvider implements ImportProviderInterface
{
  public function __construct(
    private RelationTypeRepository $relationTypeRepo,
    private ValidatorInterface $validator,
    private LearningOutcomeRepository $learningOutcomeRepository,
    private EntityManagerInterface $em,
  ) {
  }

  #[Override]
  public static function getName(): string
  {
    return 'linked-simple-node';
  }

  #[Override]
  public function isAvailable(StudyArea $studyArea): bool
  {
    return true;
  }

  #[Override]
  public function import(StudyArea $studyArea, array $jsonData): void
  {
    // Check fields
    if (!array_key_exists('nodes', $jsonData) || !is_array($jsonData['nodes'])
      || !array_key_exists('links', $jsonData) || !is_array($jsonData['links'])
    ) {
      throw new DataImportException('Expected "nodes" and "links" properties to be an array!');
    }

    // Resolve the link types
    $linkTypes = [];
    foreach ($jsonData['links'] as $jsonLink) {
      if (!array_key_exists('relationName', $jsonLink)) {
        throw new DataImportException(
          sprintf('Missing required "relationName" property on link: %s', json_encode($jsonLink)));
      }

      // Check whether already cached
      $linkName = $jsonLink['relationName'];
      if (!array_key_exists($linkName ?? '', $linkTypes)) {
        // Retrieve from database
        $linkType = $this->relationTypeRepo->findOneBy(['name' => $linkName, 'studyArea' => $studyArea]);
        if ($linkType) {
          $linkTypes[$linkName] = $linkType;
        } else {
          // Create new link type
          $linkTypes[$linkName] = new RelationType()->setStudyArea($studyArea)->setName($linkName);
          if ($this->validator->validate($linkTypes[$linkName])->count() > 0) {
            throw new DataImportException(
              sprintf('Could not create the relation type: %s', json_encode($jsonLink)));
          }
          $this->em->persist($linkTypes[$linkName]);
        }
      }
    }

    // Required flush to generate identifiers
    $this->em->flush();

    // Create a new concept for every entry
    /** @var Concept[] $concepts */
    $concepts = [];
    foreach ($jsonData['nodes'] as $key => $jsonNode) {
      if (!array_key_exists('label', $jsonNode) || $jsonNode['label'] === null) {
        throw new DataImportException(
          sprintf('Missing required "label" property on node: %s', json_encode($jsonNode)));
      }

      $concepts[$key] = new Concept()->setName($jsonNode['label']);
      if (array_key_exists('definition', $jsonNode) && $jsonNode['definition'] !== null) {
        $concepts[$key]->setDefinition($jsonNode['definition']);
      }

      if (array_key_exists('explanation', $jsonNode) && $jsonNode['explanation'] !== null) {
        $theoryExplanation = $concepts[$key]->getTheoryExplanation();
        $theoryExplanation->setText($jsonNode['explanation']);

        if ($this->validator->validate($theoryExplanation)->count() > 0) {
          throw new DataImportException(
            sprintf('Could not create the concept explanation: %s', json_encode($theoryExplanation)));
        }
      }

      if (array_key_exists('introduction', $jsonNode) && $jsonNode['introduction'] !== null) {
        $introduction = $concepts[$key]->getIntroduction();
        $introduction->setText($jsonNode['introduction']);

        if ($this->validator->validate($introduction)->count() > 0) {
          throw new DataImportException(
            sprintf('Could not create the concept introduction: %s', json_encode($introduction)));
        }
      }

      if (array_key_exists('examples', $jsonNode) && $jsonNode['examples'] !== null) {
        $examples = $concepts[$key]->getExamples();
        $examples->setText($jsonNode['examples']);

        if ($this->validator->validate($examples)->count() > 0) {
          throw new DataImportException(
            sprintf('Could not create the concept examples: %s', json_encode($examples)));
        }
      }

      if (array_key_exists('howTo', $jsonNode) && $jsonNode['howTo'] !== null) {
        $howTo = $concepts[$key]->getHowTo();
        $howTo->setText($jsonNode['howTo']);

        if ($this->validator->validate($howTo)->count() > 0) {
          throw new DataImportException(
            sprintf('Could not create the concept how to: %s', json_encode($howTo)));
        }
      }

      if (array_key_exists('selfAssessment', $jsonNode) && $jsonNode['selfAssessment'] !== null) {
        $selfAssessment = $concepts[$key]->getSelfAssessment();
        $selfAssessment->setText($jsonNode['selfAssessment']);

        if ($this->validator->validate($selfAssessment)->count() > 0) {
          throw new DataImportException(
            sprintf('Could not create the concept self assessment: %s', json_encode($selfAssessment)));
        }
      }

      $concepts[$key]->setStudyArea($studyArea);
      if ($this->validator->validate($concepts[$key])->count() > 0) {
        throw new DataImportException(
          sprintf('Could not create the concept: %s', json_encode($jsonNode)));
      }
      $this->em->persist($concepts[$key]);
    }

    // Create the links
    foreach ($jsonData['links'] as $jsonLink) {
      if (!array_key_exists('target', $jsonLink)
        || !array_key_exists('source', $jsonLink)) {
        throw new DataImportException(
          sprintf('Missing one ore more required properties "target", "relationName" or "source" from link: %s', json_encode($jsonLink)));
      }

      if (!array_key_exists($jsonLink['source'] ?? '', $concepts)) {
        throw new DataImportException(
          sprintf('Link references non-existing source node: %s', json_encode($jsonLink)));
      }
      if (!array_key_exists($jsonLink['target'] ?? '', $concepts)) {
        throw new DataImportException(
          sprintf('Link references non-existing target node: %s', json_encode($jsonLink)));
      }

      $relation = new ConceptRelation();
      $relation->setTarget($concepts[$jsonLink['target']]);
      $relation->setRelationType($linkTypes[$jsonLink['relationName']]);
      $concepts[$jsonLink['source']]->addOutgoingRelation($relation);
      if ($this->validator->validate($relation)->count() > 0) {
        throw new DataImportException(
          sprintf('Could not create the concept relation: %s', json_encode($jsonLink)));
      }
    }

    if (array_key_exists('learningOutcomes', $jsonData)) {
      if (!is_array($jsonData['learningOutcomes'])) {
        throw new DataImportException('When set, the "learningOutcomes" property must be an array!');
      }

      $learningOutcomeNumber = $this->learningOutcomeRepository->findUnusedNumberInStudyArea($studyArea);
      foreach ($jsonData['learningOutcomes'] as $jsonLearningOutcome) {
        if (!array_key_exists('name', $jsonLearningOutcome)
          || !array_key_exists('content', $jsonLearningOutcome)
          || !array_key_exists('nodes', $jsonLearningOutcome)) {
          throw new DataImportException(
            sprintf('Missing one ore more required properties "name", "content" or "nodes" from learning outcome: %s', json_encode($jsonLearningOutcome)));
        }

        if (!is_array($jsonLearningOutcome['nodes'])) {
          throw new DataImportException(
            sprintf('The "nodes" property must be an array in learning outcome: %s', json_encode($jsonLearningOutcome)));
        }

        $learningOutcome = new LearningOutcome();
        $learningOutcome->setName($jsonLearningOutcome['name']);
        $learningOutcome->setText($jsonLearningOutcome['content']);
        $learningOutcome->setNumber($learningOutcomeNumber);
        $learningOutcome->setStudyArea($studyArea);
        foreach ($jsonLearningOutcome['nodes'] as $linkedConceptKey) {
          if (!array_key_exists($linkedConceptKey ?? '', $concepts)) {
            throw new DataImportException(
              sprintf('The referenced node %d does not exist in learning outcome: %s', $linkedConceptKey, json_encode($jsonLearningOutcome)));
          }

          $concepts[$linkedConceptKey]->addLearningOutcome($learningOutcome);
        }
        if ($this->validator->validate($learningOutcome)->count() > 0) {
          throw new DataImportException(
            sprintf('Could not create the concept learning outcome: %s', json_encode($jsonLearningOutcome)));
        }
        $this->em->persist($learningOutcome);
        $learningOutcomeNumber++;
      }
    }

    if (array_key_exists('externalResources', $jsonData)) {
      if (!is_array($jsonData['externalResources'])) {
        throw new DataImportException('When set, the "externalResources" property must be an array!');
      }

      foreach ($jsonData['externalResources'] as $jsonExternalResource) {
        if (!array_key_exists('title', $jsonExternalResource)
          || !array_key_exists('nodes', $jsonExternalResource)) {
          throw new DataImportException(
            sprintf('Missing one ore more required properties "title" or "nodes" from external resource: %s', json_encode($jsonExternalResource)));
        }

        if (!is_array($jsonExternalResource['nodes'])) {
          throw new DataImportException(
            sprintf('The "nodes" property must be an array in external resource: %s', json_encode($jsonExternalResource)));
        }

        // Create the external resource
        $externalResource = new ExternalResource()
          ->setTitle($jsonExternalResource['title'])
          ->setStudyArea($studyArea);
        if (array_key_exists('description', $jsonExternalResource)) {
          $externalResource->setDescription($jsonExternalResource['description']);
        }
        if (array_key_exists('url', $jsonExternalResource)) {
          $externalResource->setUrl($jsonExternalResource['url']);
        }

        // Map to related concepts
        foreach ($jsonExternalResource['nodes'] as $linkedConceptKey) {
          if (!array_key_exists($linkedConceptKey ?? '', $concepts)) {
            throw new DataImportException(
              sprintf('The referenced node %d does not exist in external resource: %s', $linkedConceptKey, json_encode($jsonExternalResource)));
          }

          $concepts[$linkedConceptKey]->addExternalResource($externalResource);
        }

        // Validate & persist
        if ($this->validator->validate($externalResource)->count() > 0) {
          throw new DataImportException(
            sprintf('Could not create the external resource: %s', json_encode($jsonExternalResource)));
        }
        $this->em->persist($externalResource);
      }
    }

    // Tags
    if (array_key_exists('tags', $jsonData)) {
      if (!is_array($jsonData['tags'])) {
        throw new DataImportException('When set, the "tags" property must be an array!');
      }

      foreach ($jsonData['tags'] as $jsonTag) {
        if (!array_key_exists('name', $jsonTag)
          || !array_key_exists('nodes', $jsonTag)) {
          throw new DataImportException(
            sprintf('Missing one ore more required properties "name" or "nodes" from tag: %s', json_encode($jsonTag)));
        }
        if (!is_array($jsonTag['nodes'])) {
          throw new DataImportException(
            sprintf('The "nodes" property must be an array in tag: %s', json_encode($jsonTag)));
        }

        // Create the tag
        $tag = new Tag()
          ->setName($jsonTag['name'])
          ->setStudyArea($studyArea);

        if (array_key_exists('description', $jsonTag)) {
          $tag->setDescription($jsonTag['description']);
        }

        if (array_key_exists('color', $jsonTag)) {
          $tag->setColor($jsonTag['color']);
        }

        // Map to related concepts
        foreach ($jsonTag['nodes'] as $taggedConceptKey) {
          if (!array_key_exists($taggedConceptKey ?? '', $concepts)) {
            throw new DataImportException(
              sprintf('The referenced node %d does not exist in tag: %s', $taggedConceptKey, json_encode($jsonTag)));
          }

          $concepts[$taggedConceptKey]->addTag($tag);
        }

        // Validate & persist
        if ($this->validator->validate($tag)->count() > 0) {
          throw new DataImportException(
            sprintf('Could not create the tag: %s', json_encode($jsonTag)));
        }
        $this->em->persist($tag);
      }
    }

    // Prior knowledge
    if (array_key_exists('priorKnowledge', $jsonData)) {
      foreach ($jsonData['priorKnowledge'] as $jsonPriorKnowledge) {
        if (!array_key_exists('node', $jsonPriorKnowledge) || !array_key_exists('isPriorKnowledgeOf', $jsonPriorKnowledge)) {
          throw new DataImportException(
            sprintf('Missing one ore more required properties "node" or "isPriorKnowledgeOf" from prior knowledge: %s', json_encode($jsonPriorKnowledge)));
        }

        if (!array_key_exists($jsonPriorKnowledge['node'] ?? '', $concepts)) {
          throw new DataImportException(
            sprintf('Prior knowledge references non-existing "node": %s', json_encode($jsonPriorKnowledge)));
        }

        // Map prior knowledge to concepts
        foreach ($jsonPriorKnowledge['isPriorKnowledgeOf'] as $priorKnowledgeConceptKey) {
          if (!array_key_exists($priorKnowledgeConceptKey ?? '', $concepts)) {
            throw new DataImportException(
              sprintf('The referenced node %d does not exist in "isPriorKnowledgeOf": %s', $priorKnowledgeConceptKey, json_encode($jsonPriorKnowledge)));
          }
          $concepts[$jsonPriorKnowledge['node']]->addPriorKnowledge($concepts[$priorKnowledgeConceptKey]);
        }
      }
    }

    // Contributors
    if (array_key_exists('contributors', $jsonData)) {
      foreach ($jsonData['contributors'] as $jsonContributor) {
        if (!array_key_exists('name', $jsonContributor) || !array_key_exists('nodes', $jsonContributor)) {
          throw new DataImportException(
            sprintf('Missing one ore more required properties "nodes" or "name" from contributors: %s', json_encode($jsonContributor)));
        }

        if (!is_array($jsonContributor['nodes'])) {
          throw new DataImportException(
            sprintf('The "nodes" property must be an array in contributors: %s', json_encode($jsonContributor)));
        }

        // Create the contributor
        $contributor = new Contributor()
          ->setName($jsonContributor['name'])
          ->setStudyArea($studyArea);

        if (array_key_exists('description', $jsonContributor)) {
          $contributor->setDescription($jsonContributor['description']);
        }

        if (array_key_exists('url', $jsonContributor)) {
          $contributor->setUrl($jsonContributor['url']);
        }

        if (array_key_exists('email', $jsonContributor)) {
          $contributor->setEmail($jsonContributor['email']);
        }

        // Map contributor to concepts
        foreach ($jsonContributor['nodes'] as $contributorConceptKey) {
          if (!array_key_exists($contributorConceptKey ?? '', $concepts)) {
            throw new DataImportException(
              sprintf('The referenced node %d does not exist in contributor: %s', $contributorConceptKey, json_encode($jsonContributor)));
          }
          $concepts[$contributorConceptKey]->addContributor($contributor);
        }
        // Validate & persist
        if ($this->validator->validate($contributor)->count() > 0) {
          throw new DataImportException(
            sprintf('Could not create the contributor: %s', json_encode($jsonContributor)));
        }
        $this->em->persist($contributor);
      }
    }

    // aliases for study area field configuration
    if (array_key_exists('aliases', $jsonData)) {
      $studyAreaConfiguration = $studyArea->getFieldConfiguration() ?: new StudyAreaFieldConfiguration();
      $jsonAliases            = $jsonData['aliases'];

      if (array_key_exists('definition', $jsonAliases)) {
        $studyAreaConfiguration->conceptDefinitionName = $jsonAliases['definition'];
      }

      if (array_key_exists('explanation', $jsonAliases)) {
        $studyAreaConfiguration->conceptTheoryExplanationName = $jsonAliases['definition'];
      }

      if (array_key_exists('introduction', $jsonAliases)) {
        $studyAreaConfiguration->conceptIntroductionName = $jsonAliases['introduction'];
      }

      if (array_key_exists('examples', $jsonAliases)) {
        $studyAreaConfiguration->conceptExamplesName = $jsonAliases['examples'];
      }

      if (array_key_exists('selfAssessment', $jsonAliases)) {
        $studyAreaConfiguration->conceptSelfAssessmentName= $jsonAliases['selfAssessment'];
      }

      if (array_key_exists('howTo', $jsonAliases)) {
        $studyAreaConfiguration->conceptHowtoName= $jsonAliases['howTo'];
      }

      if (array_key_exists('learningOutcomes', $jsonAliases)) {
        $studyAreaConfiguration->learningOutcomeObjName= $jsonAliases['learningOutcomes'];
      }

      if (array_key_exists('skills', $jsonAliases)) {
        $studyAreaConfiguration->skillObjName= $jsonAliases['skills'];
      }

      if (array_key_exists('priorKnowledge', $jsonAliases)) {
        $studyAreaConfiguration->conceptPriorKnowledgeName= $jsonAliases['priorKnowledge'];
      }

      $studyArea->setFieldConfiguration($studyAreaConfiguration);
    }
  }
}
