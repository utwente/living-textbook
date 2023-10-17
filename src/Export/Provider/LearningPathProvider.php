<?php

namespace App\Export\Provider;

use App\Entity\StudyArea;
use App\Export\ExportService;
use App\Export\ProviderInterface;
use App\Repository\LearningPathRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LearningPathProvider implements ProviderInterface
{
  private LearningPathRepository $learningPathRepository;

  private SerializerInterface $serializer;

  public function __construct(LearningPathRepository $learningPathRepository, SerializerInterface $serializer)
  {
    $this->learningPathRepository = $learningPathRepository;
    $this->serializer             = $serializer;
  }

  public function getName(): string
  {
    return 'learning-path';
  }

  public function getPreview(): string
  {
    return <<<'EOT'
[
    {
        "elements": [
            {
                "next": <next-link-id>,
                "concept": {
                    "isEmpty": <is-empty>
                    "name": "<concept-name>",
                    "id": <concept-id>
                },
                "description": "<link-description>",
                "id": <link-id>
            }
        ],
        "name": "<name>",
        "question": "<question>",
        "id": <id>
    },
]
EOT;
  }

  public function export(StudyArea $studyArea): Response
  {
    $learningPaths = $this->learningPathRepository->findForStudyArea($studyArea);

    // Create JSON data
    $json = $this->serializer->serialize($learningPaths, 'json',
      /* @phan-suppress-next-line PhanTypeMismatchArgument */
      SerializationContext::create()->setGroups(['Default', 'lp_export']));

    $response = new JsonResponse($json, Response::HTTP_OK, [], true);
    ExportService::contentDisposition($response, sprintf('%s_learning_path_export.json', $studyArea->getName()));

    return $response;
  }
}
