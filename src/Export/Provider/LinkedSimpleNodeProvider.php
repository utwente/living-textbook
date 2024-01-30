<?php

namespace App\Export\Provider;

use App\Entity\Concept;
use App\Entity\StudyArea;
use App\Export\ExportService;
use App\Export\ProviderInterface;
use App\Naming\NamingService;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\ContributorRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use App\Repository\RelationTypeRepository;
use App\Repository\TagRepository;
use App\Router\LtbRouter;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LinkedSimpleNodeProvider implements ProviderInterface
{
  /** @var ConceptRepository */
  private $conceptRepository;
  /** @var ConceptRelationRepository */
  private $conceptRelationRepository;
  /** @var ContributorRepository */
  private $contributorRepository;
  /** @var ExternalResourceRepository */
  private $externalResourceRepository;
  /** @var LearningOutcomeRepository */
  private $learningOutcomeRepository;
  /** @var NamingService */
  private $namingService;
  /** @var RelationTypeRepository */
  private $relationTypeRepository;
  /** @var TagRepository */
  private $tagRepository;
  /** @var LtbRouter */
  private $router;
  /** @var SerializerInterface */
  private $serializer;

  public function __construct(
      ConceptRepository $conceptRepository, ConceptRelationRepository $conceptRelationRepository,
      ContributorRepository $contributorRepository, ExternalResourceRepository $externalResourceRepository,
      LearningOutcomeRepository $learningOutcomeRepository, RelationTypeRepository $relationTypeRepository,
      TagRepository $tagRepository, SerializerInterface $serializer, NamingService $namingService,
      LtbRouter $router)
  {
    $this->conceptRepository          = $conceptRepository;
    $this->conceptRelationRepository  = $conceptRelationRepository;
    $this->contributorRepository      = $contributorRepository;
    $this->externalResourceRepository = $externalResourceRepository;
    $this->learningOutcomeRepository  = $learningOutcomeRepository;
    $this->relationTypeRepository     = $relationTypeRepository;
    $this->tagRepository              = $tagRepository;
    $this->serializer                 = $serializer;
    $this->namingService              = $namingService;
    $this->router                     = $router;
  }

  /** {@inheritdoc} */
  public function getName(): string
  {
    return 'linked-simple-node';
  }

  /** {@inheritdoc} */
  public function getPreview(): string
  {
    $names      = $this->namingService->get();
    $fieldNames = $names->concept();

    return sprintf(<<<'EOT'
{
    "id": "<studyarea-id>",
    "dateCreated": "<studyarea-date-created>",
    "lastUpdated": "<studyarea-date-last-updated>",
    "datePublished": "<studyarea-publishing-date>",
    "nodes": [
        {
            "instance": "<concept-instance>",
            "label": "<concept-name>",
            "link": "<concept-url>",
            "numberOfLinks": <number-of-relations>,            
            "definition": "<concept-definition>",
            "explanation": "<concept-theory-explanation>",
            "introduction": "<concept-introduction>",
            "examples": "<concept-examples>",
            "selfAssessment": "<concept-self-assessment>",
            "howTo": "<concept-how-to>",
            "additionalResources": "<concept-additional-resources>",
            "imagePath": "<concept-image-path>",            
        }
    ],
    "links": [
        {
            "target": <target-id>,
            "source": <source-id>,
            "relationName": "<relation-name>"
        }
    ],
    "contributors": [
        {
            "nodes": [<node-ids>],
            "name": "<contributor-name>",
            "description": "<contributor-description>",
            "url": "<contributor-url>",
            "email": "<contributor-email>"
        }
    ],
    "externalResources": [
        {
            "nodes": [<node-ids>],
            "title": "<external-resource-title>",
            "description": "<external-resource-description>",
            "url": "<external-resource-url>",
        }
    ],
    "learningOutcomes": [
        {
            "nodes": [<node-ids>],
            "number": "<learning_outcome-number>",
            "name": "<learning_outcome-name>",
            "content": "<learning_outcome-content>",
        }
    ],
    "tags": [
      {
          "nodes": [<node-ids>],
          "color": "<tag-color>",
          "name": "<tag-name>",
          "description": "<tag-description>",
      }
    ],
    "priorKnowledge": [
      {
          "node": "<node-id>",
          "isPriorKnowledgeOf": [<node-ids>],          
      }
    ],
    "aliases" : [
      "definition": "%1$s",
      "explanation": "%2$s",
      "introduction": "%3$s",
      "examples": "%4$s",
      "selfAssessment": "%5$s",
      "howTo": "%6$s",
      "learningOutcomes": "%7$s",
      "priorKnowledge": "%8$s",
      "additionalResources": "%9$s",
      "imagePath": "%10$s",
    ]
}
EOT,        
        $fieldNames->definition(),
        $fieldNames->theoryExplanation(),
        $fieldNames->introduction(),
        $fieldNames->examples(),
        $fieldNames->selfAssessment(),
        $fieldNames->howTo(),
        $names->learningOutcome()->obj(),
        $fieldNames->priorKnowledge(),
        $fieldNames->additionalResources(),
        $fieldNames->imagePath(),
    );
  }

  /** {@inheritdoc} */
  public function export(StudyArea $studyArea): Response
  {
    /** @noinspection PhpUnusedLocalVariableInspection Retrieve the relation types as cache */
    $relationTypes = $this->relationTypeRepository->findBy(['studyArea' => $studyArea]);

    // Retrieve the concepts
    $concepts          = $this->conceptRepository->findForStudyAreaOrderedByName($studyArea);
    $links             = $this->conceptRelationRepository->findByConcepts($concepts);
    $contributors      = $this->contributorRepository->findForStudyArea($studyArea);
    $externalResources = $this->externalResourceRepository->findForStudyAreaOrderedByTitle($studyArea);
    $learningOutcomes  = $this->learningOutcomeRepository->findForStudyAreaOrderedByName($studyArea);
    $tags              = $this->tagRepository->findForStudyArea($studyArea);

    // Detach the data from the ORM
    $idMap = [];
    foreach ($concepts as $key => $concept) {
      $idMap[$concept->getId()] = $key;
    }

    // Create link data
    $mappedLinks = [];
    foreach ($links as &$link) {
      $mappedLinks[] = [
          'target'       => $idMap[$link->getTargetId()],
          'source'       => $idMap[$link->getSourceId()],
          'relationName' => $link->getRelationName(),
      ];
    }

    // Create contributors data
    $mappedContributors = [];
    foreach ($contributors as $contributor) {
      $mappedContributors[] = [
          'nodes'       => $contributor->getConcepts()->map(fn (Concept $concept)       => $idMap[$concept->getId()]),
          'name'        => $contributor->getName(),
          'description' => $contributor->getDescription(),
          'url'         => $contributor->getUrl(),
          'email'       => $contributor->getEmail(),
      ];
    }

    // Create external resource data
    $mappedExternalResources = [];
    foreach ($externalResources as $externalResource) {
      $mappedExternalResources[] = [
          'nodes'       => $externalResource->getConcepts()->map(fn (Concept $concept)       => $idMap[$concept->getId()]),
          'title'       => $externalResource->getTitle(),
          'description' => $externalResource->getDescription(),
          'url'         => $externalResource->getUrl(),
      ];
    }

    // Create learning outcomes data
    $mappedLearningOutcomes = [];
    foreach ($learningOutcomes as $learningOutcome) {
      $mappedLearningOutcomes[] = [
          'nodes'   => $learningOutcome->getConcepts()->map(fn (Concept $concept)   => $idMap[$concept->getId()]),
          'number'  => $learningOutcome->getNumber(),
          'name'    => $learningOutcome->getName(),
          'content' => $learningOutcome->getText(),
      ];
    }

    // Create tags data
    $mappedTags = [];
    foreach ($tags as $tag) {
      $mappedTags[] = [
          'nodes'       => $tag->getConcepts()->map(fn (Concept $concept)       => $idMap[$concept->getId()]),
          'color'       => $tag->getColor(),
          'name'        => $tag->getName(),
          'description' => $tag->getDescription(),
      ];
    }

    // Create prior knowledge data
    $mappedPriorKnowledge = [];
    foreach ($concepts as $concept) {
      if (!$concept->getPriorKnowledge()->isEmpty()) {
        $mappedPriorKnowledge[] = [
          'node'               => $idMap[$concept->getId()],
          'isPriorKnowledgeOf' => $concept->getPriorKnowledge()->map(fn (Concept $priorKnowledge) => $idMap[$priorKnowledge->getId()]),
        ];
      }
    }

    // Create JSON data

    $names                 = $this->namingService->get();
    $fieldNames            = $names->concept();
    
    // Return as JSON
    $serializationContext = SerializationContext::create();
    $serializationContext->setSerializeNull(true);
    $json = $this->serializer->serialize(
          [
              'id'                  => $studyArea->getId(),
              'dateCreated'         => $studyArea->getCreatedAt(),
              'lastUpdated'         => $studyArea->getLastUpdated(),
              'datePublished'       => date('Y-m-d H:i:s'),
              'nodes' => array_map(fn (Concept $concept) => [                  
                  'instance'       => $concept->isInstance(),
                  'label'          => $concept->getName(),
                  'link'           => $this->router->generateBrowserUrl('app_concept_show', ['concept' => $concept->getId()]),
                  'numberOfLinks'  => $concept->getNumberOfLinks(),
                  'definition'     => $concept->getDefinition()->getText(),
                  'explanation'    => $concept->getTheoryExplanation()->getText(),
                  'introduction'   => $concept->getIntroduction()->getText(),
                  'examples'       => $concept->getExamples()->getText(),
                  'howTo'          => $concept->getHowTo()->getText(),
                  'selfAssessment' => $concept->getSelfAssessment()->getText(),
                  'additionalResources'     => $concept->getAdditionalResources() ? $concept->getAdditionalResources()->getText(): '',
                  'imagePath'               => $concept->getImagePath(),
              ], $concepts),
              'links'             => $mappedLinks,
              'contributors'      => $mappedContributors,
              'externalResources' => $mappedExternalResources,
              'learningOutcomes'  => $mappedLearningOutcomes,
              'tags'              => $mappedTags,
              'priorKnowledge'    => $mappedPriorKnowledge,
              'aliases'           => [
                'definition'          => $fieldNames->definition(),
                'explanation'         => $fieldNames->theoryExplanation(),
                'introduction'        => $fieldNames->introduction(),
                'examples'            => $fieldNames->examples(),
                'selfAssessment'      => $fieldNames->selfAssessment(),
                'howTo'               => $fieldNames->howTo(),
                'learningOutcomes'    => $names->learningOutcome()->obj(),
                'priorKnowledge'      => $fieldNames->priorKnowledge(),
                'additionalResources' => $fieldNames->additionalResources(),
                'imagePath'           => $fieldNames->imagePath(),
              ],
          ],
          'json', $serializationContext);

    $response = new JsonResponse($json, Response::HTTP_OK, [], true);
    ExportService::contentDisposition($response, sprintf('%s_export.json', $studyArea->getName()));

    return $response;
  }
}
