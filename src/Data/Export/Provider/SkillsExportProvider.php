<?php

namespace App\Data\Export\Provider;

use App\Data\Export\ExportProviderInterface;
use App\Data\Export\ExportService;
use App\Entity\Data\DataSkills;
use App\Entity\StudyArea;
use App\Repository\ConceptRepository;
use App\Skills\SkillAccessor;
use Drenso\Shared\Exception\NullGuard\MustNotBeEmptyException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use function array_filter;
use function array_map;
use function explode;
use function implode;
use function json_encode;
use function sprintf;

use const JSON_PRETTY_PRINT;

#[Lazy]
final readonly class SkillsExportProvider implements ExportProviderInterface
{
  public function __construct(
    private ConceptRepository $conceptRepository,
    private SerializerInterface $serializer,
    private SkillAccessor $skillAccessor,
  ) {
  }

  #[Override]
  public static function getName(): string
  {
    return 'skills';
  }

  #[Override]
  public function isAvailable(StudyArea $studyArea): bool
  {
    return $studyArea->isSkillsEnabled();
  }

  #[Override]
  public function getPreview(StudyArea $studyArea): string
  {
    $preview = <<<'EOT'
[
    {
        "concept_id": <concept-id>,
        "concept": "<concept-naam>",
        "classifiedSkills": {

EOT;

    $preview .= self::formattedSkills($this->skillAccessor);

    $preview .= <<<'EOT'

        }
    }
]
EOT;

    return $preview;
  }

  #[Override]
  public function export(StudyArea $studyArea): JsonResponse
  {
    $concepts = $this->conceptRepository->findForStudyAreaOrderedByName($studyArea);

    $data = [];
    foreach ($concepts as $concept) {
      $data[] = [
        'concept_id'       => $concept->getId(),
        'concept'          => $concept->getName(),
        'classifiedSkills' => $this->skillAccessor->getAsArrayData($concept->getSkills()),
      ];
    }

    $json     = $this->serializer->serialize($data, 'json', SerializationContext::create()->setSerializeNull(true));
    $response = new JsonResponse($json, Response::HTTP_OK, [], true);
    ExportService::contentDisposition($response, sprintf('%s_skills_export.json', $studyArea->getName()));

    return $response;
  }

  public static function formattedSkills(SkillAccessor $skillAccessor, string $ident = '        '): string
  {
    return (json_encode($skillAccessor->getAsArrayData(new DataSkills(), '<nullable-value>'), JSON_PRETTY_PRINT) ?: throw new MustNotBeEmptyException())
      |> (static fn (string $x): array => explode("\n", $x))
      |> (static fn (array $x): array => array_filter($x, static fn (string $line): bool => $line !== '{' && $line !== '}'))
      |> (static fn (array $x): array => array_map(static fn (string $line): string => $ident . $line, $x))
      |> (static fn (array $x): string => implode("\n", $x));
  }
}
