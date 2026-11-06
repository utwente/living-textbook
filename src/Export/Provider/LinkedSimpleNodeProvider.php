<?php

namespace App\Export\Provider;

use App\Entity\Concept;
use App\Entity\Data\DataSkills;
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
use App\Skills\SkillAccessor;
use Drenso\Shared\Exception\NullGuard\IdRequiredException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use function array_map;
use function sprintf;

#[Lazy]
final readonly class LinkedSimpleNodeProvider implements ProviderInterface
{
  public function __construct(
    private ConceptRepository $conceptRepository,
    private ConceptRelationRepository $conceptRelationRepository,
    private ContributorRepository $contributorRepository,
    private ExternalResourceRepository $externalResourceRepository,
    private LearningOutcomeRepository $learningOutcomeRepository,
    private RelationTypeRepository $relationTypeRepository,
    private TagRepository $tagRepository,
    private SerializerInterface $serializer,
    private NamingService $namingService,
    private LtbRouter $router,
    private SkillAccessor $skillAccessor,
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
  public function getPreview(StudyArea $studyArea): string
  {
    $names      = $this->namingService->get();
    $fieldNames = $names->concept;

    $preview = <<<'EOT'
{
    "id": "<studyarea-id>",
    "name": "<studyarea-name>",
    "description": "<studyarea-description>",
    "date_created": "<studyarea-date-created>",
    "last_updated": "<studyarea-last-updated>",
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
EOT;
    if (!$studyArea->isDeprecatedSkills()) {
      $preview .= <<<'EOT'

            "skills": {

EOT;

      $preview .= SkillsProvider::formattedSkills($this->skillAccessor, '            ');

      $preview .= <<<'EOT'

            }
EOT;
    }

    $preview .= <<<'EOT'

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
EOT;
    if ($studyArea->isDeprecatedSkills()) {
      $preview .= <<<'EOT'

    "learningOutcomes": [
        {
            "nodes": [<node-ids>],
            "number": "<learning_outcome-number>",
            "name": "<learning_outcome-name>",
            "content": "<learning_outcome-content>",
        }
    ],
EOT;
    }

    $preview .= <<<'EOT'

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
EOT;

    if ($studyArea->isDeprecatedSkills()) {
      $preview .= <<<'EOT'

      "learningOutcomes": "%7$s",
EOT;
    } else {
      $preview .= <<<'EOT'

      "skills": "%7$s",
EOT;
    }

    $preview .= <<<'EOT'

      "priorKnowledge": "%8$s",
    ]
}
EOT;

    return sprintf($preview,
      $fieldNames->definition(),
      $fieldNames->theoryExplanation(),
      $fieldNames->introduction(),
      $fieldNames->examples(),
      $fieldNames->selfAssessment(),
      $fieldNames->howTo(),
      $studyArea->isDeprecatedSkills() ? $names->learningOutcome->obj() : $names->skill->obj(),
      $fieldNames->priorKnowledge(),
    );
  }

  #[Override]
  public function export(StudyArea $studyArea): Response
  {
    /** @noinspection PhpUnusedLocalVariableInspection Retrieve the relation types as cache */
    $relationTypes = $this->relationTypeRepository->findBy(['studyArea' => $studyArea]);

    // Retrieve the concepts
    $concepts          = $this->conceptRepository->findForStudyAreaOrderedByName($studyArea);
    $links             = $this->conceptRelationRepository->findByConcepts($concepts);
    $contributors      = $this->contributorRepository->findForStudyArea($studyArea);
    $externalResources = $this->externalResourceRepository->findForStudyAreaOrderedByTitle($studyArea);
    $tags              = $this->tagRepository->findForStudyArea($studyArea);

    // Detach the data from the ORM
    $idMap = [];
    foreach ($concepts as $key => $concept) {
      $idMap[$concept->getNonNullId()] = $key;
    }

    // Create link data
    $mappedLinks = [];
    foreach ($links as &$link) {
      $mappedLinks[] = [
        'target'       => $idMap[$link->getTargetId() ?? throw new IdRequiredException()],
        'source'       => $idMap[$link->getSourceId() ?? throw new IdRequiredException()],
        'relationName' => $link->getRelationName(),
      ];
    }

    // Create contributors data
    $mappedContributors = [];
    foreach ($contributors as $contributor) {
      $mappedContributors[] = [
        'nodes'       => $contributor->getConcepts()->map(static fn (Concept $concept): mixed => $idMap[$concept->getNonNullId()]),
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
        'nodes'       => $externalResource->getConcepts()->map(static fn (Concept $concept): mixed => $idMap[$concept->getNonNullId()]),
        'title'       => $externalResource->getTitle(),
        'description' => $externalResource->getDescription(),
        'url'         => $externalResource->getUrl(),
      ];
    }

    $mappedLearningOutcomes = [];
    if ($studyArea->isDeprecatedSkills()) {
      // Create learning outcomes data
      $learningOutcomes = $this->learningOutcomeRepository->findForStudyAreaOrderedByName($studyArea);
      foreach ($learningOutcomes as $learningOutcome) {
        $mappedLearningOutcomes[] = [
          'nodes'   => $learningOutcome->getConcepts()->map(static fn (Concept $concept): mixed => $idMap[$concept->getNonNullId()]),
          'number'  => $learningOutcome->getNumber(),
          'name'    => $learningOutcome->getName(),
          'content' => $learningOutcome->getText(),
        ];
      }
    }

    // Create tags data
    $mappedTags = [];
    foreach ($tags as $tag) {
      $mappedTags[] = [
        'nodes'       => $tag->getConcepts()->map(static fn (Concept $concept): mixed => $idMap[$concept->getNonNullId()]),
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
          'node'               => $idMap[$concept->getNonNullId()],
          'isPriorKnowledgeOf' => $concept->getPriorKnowledge()->map(static fn (Concept $priorKnowledge): mixed => $idMap[$priorKnowledge->getNonNullId()]),
        ];
      }
    }

    // Create JSON data
    $names      = $this->namingService->get();
    $fieldNames = $names->concept;
    $data       = [
      'id'           => $studyArea->getNonNullId(),
      'name'         => $studyArea->getName(),
      'description'  => $studyArea->getDescription(),
      'date_created' => $studyArea->getCreatedAt(),
      'last_updated' => $studyArea->getLastUpdated(),
      'nodes' => array_map(
        function (Concept $concept) use ($studyArea): array {
          $nodeData = [
            'instance'       => $concept->isInstance(),
            'label'          => $concept->getName(),
            'link'           => $this->router->generateBrowserUrl('app_concept_show', ['concept' => $concept->getId()]),
            'numberOfLinks'  => $concept->getNumberOfLinks(),
            'definition'     => $concept->getDefinition(),
            'explanation'    => $concept->getTheoryExplanation()->getText(),
            'introduction'   => $concept->getIntroduction()->getText(),
            'examples'       => $concept->getExamples()->getText(),
            'howTo'          => $concept->getHowTo()->getText(),
            'selfAssessment' => $concept->getSelfAssessment()->getText(),
          ];

          if (!$studyArea->isDeprecatedSkills()) {
            $nodeData['skills'] = $this->skillAccessor->getAsArrayData($concept->getSkills());
          }

          return $nodeData;
        },
        $concepts,
      ),
      'links'             => $mappedLinks,
      'contributors'      => $mappedContributors,
      'externalResources' => $mappedExternalResources,
      'tags'              => $mappedTags,
      'priorKnowledge'    => $mappedPriorKnowledge,
      'aliases'           => [
        'definition'     => $fieldNames->definition(),
        'explanation'    => $fieldNames->theoryExplanation(),
        'introduction'   => $fieldNames->introduction(),
        'examples'       => $fieldNames->examples(),
        'selfAssessment' => $fieldNames->selfAssessment(),
        'howTo'          => $fieldNames->howTo(),
        'skills'         => $names->skill->obj(),
        'priorKnowledge' => $fieldNames->priorKnowledge(),
      ],
    ];

    if ($studyArea->isDeprecatedSkills()) {
      $data['learningOutcomes']            = $mappedLearningOutcomes;
      $data['aliases']['learningOutcomes'] = $names->learningOutcome->obj();
    } else {
      $data['aliases']['skills'] = $names->skill->obj();
    }

    // Return as JSON
    $json     = $this->serializer->serialize($data, 'json', SerializationContext::create()->setSerializeNull(true));
    $response = new JsonResponse($json, Response::HTTP_OK, [], true);
    ExportService::contentDisposition($response, sprintf('%s_export.json', $studyArea->getName()));

    return $response;
  }
}
