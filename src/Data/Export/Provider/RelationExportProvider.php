<?php

namespace App\Data\Export\Provider;

use App\Data\Export\ExportProviderInterface;
use App\Entity\ConceptRelation;
use App\Entity\StudyArea;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\RelationTypeRepository;
use Drenso\Shared\Exception\NullGuard\ObjectRequiredException;
use Drenso\Shared\Helper\SpreadsheetHelper;
use Override;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function sprintf;
use function usort;

#[Lazy]
final readonly class RelationExportProvider implements ExportProviderInterface
{
  public function __construct(
    private ConceptRepository $conceptRepository,
    private ConceptRelationRepository $conceptRelationRepository,
    private RelationTypeRepository $relationTypeRepository,
    private SpreadsheetHelper $spreadsheetHelper)
  {
  }

  #[Override]
  public static function getName(): string
  {
    return 'relation';
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
"From";"From name";"To";"To name";"Relation"
"<id>";"<concept-name>";"<id>";"<concept-name>";"<relation>"
"<id>";"<concept-name>";"<id>";"<concept-name>";"<relation>"
"<id>";"<concept-name>";"<id>";"<concept-name>";"<relation>"
EOT;
  }

  public function getSpreadsheet(StudyArea $studyArea): Spreadsheet
  {
    /** @noinspection PhpUnusedLocalVariableInspection Retrieve the relation types as cache */
    $relationTypes = $this->relationTypeRepository->findBy(['studyArea' => $studyArea]);

    // Retrieve the concepts
    $concepts = $this->conceptRepository->findForStudyAreaOrderedByName($studyArea);
    $links    = $this->conceptRelationRepository->findByConcepts($concepts);

    // Sort them first of source name, than on target name
    usort($links, static function (ConceptRelation $a, ConceptRelation $b): int {
      if ($a->getSourceId() === $b->getSourceId()) {
        $aTarget = $a->getTarget() ?? throw new ObjectRequiredException();
        $bTarget = $b->getTarget() ?? throw new ObjectRequiredException();

        return $aTarget->getName() <=> $bTarget->getName();
      }

      $aSource = $a->getSource() ?? throw new ObjectRequiredException();
      $bSource = $b->getSource() ?? throw new ObjectRequiredException();

      return $aSource->getName() <=> $bSource->getName();
    });

    $row         = 1;
    $column      = 1;
    $spreadSheet = new Spreadsheet();
    $sheet       = $spreadSheet->getSheet(0);
    $sheet->setCellValue(CellAddress::fromColumnAndRow($column++, $row), 'From');
    $sheet->setCellValue(CellAddress::fromColumnAndRow($column++, $row), 'From name');
    $sheet->setCellValue(CellAddress::fromColumnAndRow($column++, $row), 'To');
    $sheet->setCellValue(CellAddress::fromColumnAndRow($column++, $row), 'To name');
    $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row++), 'Relation');
    foreach ($links as $link) {
      $column = 1;
      $sheet->setCellValue(CellAddress::fromColumnAndRow($column++, $row), $link->getSourceId());
      $source = $link->getSource() ?? throw new ObjectRequiredException();
      $sheet->setCellValue(CellAddress::fromColumnAndRow($column++, $row), $source->getName());
      $sheet->setCellValue(CellAddress::fromColumnAndRow($column++, $row), $link->getTargetId());
      $target = $link->getTarget() ?? throw new ObjectRequiredException();
      $sheet->setCellValue(CellAddress::fromColumnAndRow($column++, $row), $target->getName());
      $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row++), $link->getRelationName());
    }

    return $spreadSheet;
  }

  #[Override]
  public function export(StudyArea $studyArea): StreamedResponse
  {
    return $this->spreadsheetHelper->createCsvResponse($this->getSpreadsheet($studyArea),
      sprintf('%s_concept_relation_export.csv', $studyArea->getName()));
  }
}
