<?php

namespace App\Data\Export\Provider;

use App\Data\Export\ExportProviderInterface;
use App\Data\Export\ExportService;
use App\Entity\StudyArea;
use App\Repository\LearningPathRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function sprintf;

#[Lazy]
final readonly class LearningPathExportProvider implements ExportProviderInterface
{
  public function __construct(
    private LearningPathRepository $learningPathRepository,
    private SerializerInterface $serializer,
  ) {
  }

  #[Override]
  public static function getName(): string
  {
    return 'learning-path';
  }

  #[Override]
  public function isAvailable(StudyArea $studyArea): bool
  {
    return true;
  }

  #[Override]
  public function getPreview(StudyArea $studyArea): string
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

  #[Override]
  public function export(StudyArea $studyArea): Response
  {
    $learningPaths = $this->learningPathRepository->findForStudyArea($studyArea);

    // Create JSON data
    $json = $this->serializer->serialize($learningPaths, 'json',
      SerializationContext::create()->setGroups(['Default', 'lp_export']));

    $response = new JsonResponse($json, Response::HTTP_OK, [], true);
    ExportService::contentDisposition($response, sprintf('%s_learning_path_export.json', $studyArea->getName()));

    return $response;
  }
}
