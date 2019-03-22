<?php

namespace App\Excel;

use App\Entity\Concept;
use App\Entity\RelationType;
use App\Entity\StudyArea;
use App\Export\ExportService;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\RelationTypeRepository;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class StudyAreaStatusBuilder
 *
 * This class is used to build a Excel sheet with the current study area status
 */
class StudyAreaStatusBuilder
{
  /** @var TranslatorInterface */
  private $translator;

  /** @var RelationTypeRepository */
  private $relationTypeRepo;

  /** @var ConceptRepository */
  private $conceptRepo;

  /** @var ConceptRelationRepository */
  private $conceptRelationRepo;

  /** @var Spreadsheet */
  private $spreadsheet;

  /** @var StudyArea */
  private $studyArea;

  /** @var RelationType[]|Collection */
  private $relationTypes;

  /** @var Concept[]|Collection */
  private $concepts;

  /**
   * StudyAreaStatusBuilder constructor.
   *
   * @param TranslatorInterface       $translator
   * @param ConceptRepository         $conceptRepo
   * @param ConceptRelationRepository $conceptRelationRepo
   * @param RelationTypeRepository    $relationTypeRepo
   */
  public function __construct(TranslatorInterface $translator, ConceptRepository $conceptRepo,
                              ConceptRelationRepository $conceptRelationRepo, RelationTypeRepository $relationTypeRepo)
  {
    $this->translator          = $translator;
    $this->conceptRelationRepo = $conceptRelationRepo;
    $this->conceptRepo         = $conceptRepo;
    $this->relationTypeRepo    = $relationTypeRepo;
  }

  /**
   * Create the excel response
   *
   * @param Request   $request
   * @param StudyArea $studyArea
   *
   * @return Response
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  public function build(Request $request, StudyArea $studyArea): Response
  {
    // Save study area
    $this->studyArea = $studyArea;

    // Retrieve the relation types
    $this->relationTypes = $this->relationTypeRepo->findBy(['studyArea' => $studyArea]);

    // Retrieve the concepts
    $this->concepts = $this->conceptRepo->findForStudyAreaOrderedByName($studyArea, true);

    // Create spreadsheet
    $this->spreadsheet = new Spreadsheet();
    $this->spreadsheet->getProperties()->setCreator($this->studyArea->getOwner()->getDisplayName())
        ->setTitle($this->studyArea->getName())
        ->setSubject($this->translator->trans('excel.subject', ['%item%' => $this->studyArea->getName()]))
        ->setDescription($this->translator->trans('excel.description', ['%item%' => $this->studyArea->getName()]));

    // Create content
    $this->spreadsheet->removeSheetByIndex(0);
    $this->addGeneralInfoSheet();
    $this->addGeneralConceptStatisticsSheet();
    $this->addGeneralRelationshipStatisticsSheet();
    $this->addDetailedConceptOverviewSheet();
    $this->addDetailedRelationshipsOverviewSheet();

    // Reset active sheet index and selected cells
    foreach ($this->spreadsheet->getAllSheets() as $sheet) {
      $sheet->setSelectedCellByColumnAndRow(1, 1);
    }
    $this->spreadsheet->setActiveSheetIndex(0);

    // Create writer
    $writer   = new Xlsx($this->spreadsheet);
    $response = new StreamedResponse(
        function () use ($writer) {
          $writer->save('php://output');
        });
    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
    ExportService::contentDisposition($response, sprintf('%s_status.xlsx', $studyArea->getName()));

    // Return the response
    $response->prepare($request);
    $response->sendHeaders();
    $response->sendContent();

    return $response;
  }

  /**
   * Creates a new sheet with the specified name
   *
   * @param string $name
   *
   * @return Worksheet
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  private function createSheet(string $name): Worksheet
  {
    $sheet = new Worksheet($this->spreadsheet, $this->translator->trans($name));
    $this->spreadsheet->addSheet($sheet);

    return $sheet;
  }

  /**
   * @param Worksheet $sheet
   * @param int       $column
   * @param int       $row
   * @param bool      $value
   */
  private function setCellBooleanValue(Worksheet &$sheet, int $column, int $row, bool $value)
  {
    $this->setCellTranslatedValue($sheet, $column, $row, $value ? 'excel.boolean.yes' : 'excel.boolean.no');
  }

  /**
   * @param Worksheet $sheet
   * @param int       $column
   * @param int       $row
   * @param string    $value
   * @param bool      $bold
   */
  private function setCellTranslatedValue(Worksheet &$sheet, int $column, int $row, string $value, bool $bold = false)
  {
    $this->setCellValue($sheet, $column, $row, $this->translator->trans($value), $bold);
  }

  /**
   * @param Worksheet $sheet
   * @param int       $column
   * @param int       $row
   * @param mixed     $value
   * @param bool      $bold
   */
  private function setCellValue(Worksheet &$sheet, int $column, int $row, $value, bool $bold = false)
  {
    $sheet->setCellValueByColumnAndRow($column, $row, $value);

    if ($bold) {
      $sheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true);
    }
  }

  /**
   * @param Worksheet $sheet
   * @param int       $column
   * @param int       $row
   * @param \DateTime $dateTime
   * @param bool      $leftAligned
   * @param bool      $bold
   */
  private function setCellDateTime(Worksheet &$sheet, int $column, int $row, \DateTime $dateTime, bool $leftAligned = false, bool $bold = false)
  {
    $this->setCellValue($sheet, $column, $row, Date::PHPToExcel($dateTime), $bold);
    $sheet->getStyleByColumnAndRow($column, $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

    if ($leftAligned) {
      $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }
  }

  /**
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  private function addGeneralInfoSheet()
  {
    $sheet = $this->createSheet('excel.sheet.general-info._tab');

    $column = 1;
    $row    = 1;

    $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
    $sheet->getColumnDimensionByColumn($column + 1)->setAutoSize(true);
    $sheet->getColumnDimensionByColumn($column + 2)->setAutoSize(true);

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-info.name', true);
    $this->setCellTranslatedValue($sheet, $column + 1, $row, $this->studyArea->getName());
    $row++;

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-info.owner', true);
    $this->setCellTranslatedValue($sheet, $column + 1, $row, $this->studyArea->getOwner()->getDisplayName());
    $row++;

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-info.access-type', true);
    $this->setCellTranslatedValue($sheet, $column + 1, $row, ucfirst($this->studyArea->getAccessType()));
    $row++;

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-info.creation-data', true);
    $this->setCellDateTime($sheet, $column + 1, $row, $this->studyArea->getCreatedAt(), true);
    $row++;

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-info.last-edit', true);
    $lastEditInfo = $this->studyArea->getLastEditInfo();
    $this->setCellDateTime($sheet, $column + 1, $row, $lastEditInfo[0], true);
    $this->setCellValue($sheet, $column + 2, $row, $lastEditInfo[1]);

    $sheet->getStyleByColumnAndRow(1, 1, $column + 2, $row)
        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
  }

  /**
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  private function addGeneralConceptStatisticsSheet()
  {
    $sheet = $this->createSheet('excel.sheet.general-concept-statistics._tab');

    $column = 1;
    $row    = 1;

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.statistics-item', true);
    $this->setCellTranslatedValue($sheet, $column + 1, $row, 'excel.sheet.general-concept-statistics.total-concepts', true);
    $this->setCellTranslatedValue($sheet, $column + 2, $row, 'excel.sheet.general-concept-statistics.no-text', true);
    $this->setCellTranslatedValue($sheet, $column + 3, $row, 'excel.sheet.general-concept-statistics.no-definition', true);
    $this->setCellTranslatedValue($sheet, $column + 4, $row, 'excel.sheet.general-concept-statistics.no-introduction', true);
    $this->setCellTranslatedValue($sheet, $column + 5, $row, 'excel.sheet.general-concept-statistics.no-prior-knowledge', true);
    $this->setCellTranslatedValue($sheet, $column + 6, $row, 'excel.sheet.general-concept-statistics.no-learning-outcomes', true);
    $this->setCellTranslatedValue($sheet, $column + 7, $row, 'excel.sheet.general-concept-statistics.no-relations', true);
    $this->setCellTranslatedValue($sheet, $column + 8, $row, 'excel.sheet.general-concept-statistics.more-relations-5', true);
    $this->setCellTranslatedValue($sheet, $column + 9, $row, 'excel.sheet.general-concept-statistics.more-relations-10', true);

    $row++;

    $column = 2;
    $counts = array_fill(0, 9, 0);
    $setter = function (Concept $concept, array &$counts, int $index, bool $condition) use (&$sheet, $column, $row) {
      if ($condition) {
        $this->setCellValue($sheet, $column + $index, $row + 1 + $counts[$index], $concept->getName());
        $counts[$index]++;
      }
    };
    foreach ($this->concepts as $concept) {
      $setter($concept, $counts, 0, true);
      $setter($concept, $counts, 1, !$concept->hasTextData());
      $setter($concept, $counts, 2, $concept->getDefinition() == '');
      $setter($concept, $counts, 3, !$concept->getIntroduction()->hasData());
      $setter($concept, $counts, 4, $concept->getPriorKnowledge()->isEmpty());
      $setter($concept, $counts, 5, $concept->getLearningOutcomes()->isEmpty());

      $relationCount = $concept->getOutgoingRelations()->count() + $concept->getIncomingRelations()->count();
      $setter($concept, $counts, 6, $relationCount == 0);
      $setter($concept, $counts, 7, $relationCount > 5);
      $setter($concept, $counts, 8, $relationCount > 10);
    }

    $column = 1;
    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.count', true);
    foreach ($counts as $key => $count) {
      $this->setCellValue($sheet, $column + 1 + $key, $row, $count);
    }

    for ($column = 1; $column <= 10; $column++) {
      $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
    }
  }

  /**
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  private function addGeneralRelationshipStatisticsSheet()
  {
    $sheet = $this->createSheet('excel.sheet.general-relationship-statistics._tab');

    $column = 1;
    $row    = 1;

    $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
    $sheet->getColumnDimensionByColumn($column + 1)->setAutoSize(true);

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-relationship-statistics.relationships', true);
    $row++;

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.statistics-item', true);
    $this->setCellTranslatedValue($sheet, $column + 1, $row, 'excel.sheet.general-relationship-statistics.types-number', true);
    $row++;

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-relationship-statistics.types');
    $this->setCellValue($sheet, $column + 1, $row, count($this->relationTypes));
    $row++;

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.statistics-item', true);
    $this->setCellTranslatedValue($sheet, $column + 1, $row, 'excel.sheet.general-relationship-statistics.number', true);
    $row++;

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-relationship-statistics.number-per-type');

    foreach ($this->relationTypes as $relationType) {
      $row++;
      $this->setCellValue($sheet, $column, $row, sprintf('  %s',
          $this->translator->trans('excel.sheet.general-relationship-statistics.type', ['%type%' => $relationType->getName()])));
      $this->setCellValue($sheet, $column + 1, $row, $this->conceptRelationRepo->getByRelationTypeCount($relationType));
    }

    $sheet->getStyleByColumnAndRow(1, 1, $column + 1, $row)
        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyleByColumnAndRow(1, 1)->getBorders()->getRight()->setBorderStyle(Border::BORDER_NONE);
    $sheet->getStyleByColumnAndRow(2, 1)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_NONE);
  }

  /**
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  private function addDetailedRelationshipsOverviewSheet()
  {
    $sheet = $this->createSheet('excel.sheet.detailed-relationships-overview._tab');

    $column = 1;
    $row    = 1;

    // Add two to count for offset
    $conceptCount = count($this->concepts) + 2;

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.concept', true);
    $this->setCellTranslatedValue($sheet, $column, $row + $conceptCount, 'excel.concept', true);
    $this->setCellTranslatedValue($sheet, $column + 1, $row, 'excel.outgoing-relations', true);
    $this->setCellTranslatedValue($sheet, $column + 1, $row + $conceptCount, 'excel.incoming-relations', true);

    $maxCol = 1;
    foreach ($this->concepts as $concept) {
      $row++;
      $column = 1;

      $this->setCellValue($sheet, $column, $row, $concept->getName());
      $this->setCellValue($sheet, $column, $row + $conceptCount, $concept->getName());

      foreach ($concept->getOutgoingRelations() as $conceptRelation) {
        $column++;
        $this->setCellValue($sheet, $column, $row,
            sprintf('* %s %s', $conceptRelation->getRelationName(), $conceptRelation->getTarget()->getName()));
        if ($column > $maxCol) $maxCol = $column;
      }

      $column = 1;
      foreach ($concept->getIncomingRelations() as $conceptRelation) {
        $column++;
        $this->setCellValue($sheet, $column, $row + $conceptCount,
            sprintf('%s %s *', $conceptRelation->getSource()->getName(), $conceptRelation->getRelationName()));
        if ($column > $maxCol) $maxCol = $column;
      }
    }

    for ($column = 1; $column <= $maxCol; $column++) {
      $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
    }

    $sheet->getStyleByColumnAndRow(1, 1, $maxCol, $row)
        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyleByColumnAndRow(2, 1, $maxCol, 1)
        ->getBorders()->getInside()->setBorderStyle(Border::BORDER_NONE);

    $sheet->getStyleByColumnAndRow(1, 1 + $conceptCount, $maxCol, $row + $conceptCount)
        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyleByColumnAndRow(2, 1 + $conceptCount, $maxCol, 1 + $conceptCount)
        ->getBorders()->getInside()->setBorderStyle(Border::BORDER_NONE);
  }

  /**
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  private function addDetailedConceptOverviewSheet()
  {
    $sheet = $this->createSheet('excel.sheet.detailed-concept-overview._tab');

    for ($column = 1; $column <= 13; $column++) {
      $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
    }

    $column = 1;
    $row    = 1;

    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.detailed-concept-overview.concept-name', true);
    $this->setCellTranslatedValue($sheet, $column + 1, $row, 'excel.sheet.detailed-concept-overview.definition', true);
    $this->setCellTranslatedValue($sheet, $column + 2, $row, 'excel.sheet.detailed-concept-overview.introduction', true);
    $this->setCellTranslatedValue($sheet, $column + 3, $row, 'excel.sheet.detailed-concept-overview.explanation', true);
    $this->setCellTranslatedValue($sheet, $column + 4, $row, 'excel.sheet.detailed-concept-overview.prior-knowledge', true);
    $this->setCellTranslatedValue($sheet, $column + 5, $row, 'excel.sheet.detailed-concept-overview.examples', true);
    $this->setCellTranslatedValue($sheet, $column + 6, $row, 'excel.sheet.detailed-concept-overview.learning-outcomes', true);
    $this->setCellTranslatedValue($sheet, $column + 7, $row, 'excel.sheet.detailed-concept-overview.how-to', true);
    $this->setCellTranslatedValue($sheet, $column + 8, $row, 'excel.sheet.detailed-concept-overview.self-assessment', true);
    $this->setCellTranslatedValue($sheet, $column + 9, $row, 'excel.sheet.detailed-concept-overview.external-links', true);
    $this->setCellTranslatedValue($sheet, $column + 10, $row, 'excel.sheet.detailed-concept-overview.number-of-relations', true);
    $this->setCellTranslatedValue($sheet, $column + 11, $row, 'excel.sheet.detailed-concept-overview.last-edit-time', true);
    $this->setCellTranslatedValue($sheet, $column + 12, $row, 'excel.sheet.detailed-concept-overview.last-editor', true);

    foreach ($this->concepts as $concept) {
      $row++;
      $this->setCellValue($sheet, $column, $row, $concept->getName());
      $this->setCellBooleanValue($sheet, $column + 1, $row, $concept->getDefinition() != '');
      $this->setCellBooleanValue($sheet, $column + 2, $row, $concept->getIntroduction()->hasData());
      $this->setCellBooleanValue($sheet, $column + 3, $row, $concept->getTheoryExplanation()->hasData());
      $this->setCellBooleanValue($sheet, $column + 4, $row, !$concept->getPriorKnowledge()->isEmpty());
      $this->setCellBooleanValue($sheet, $column + 5, $row, $concept->getExamples()->hasData());
      $this->setCellBooleanValue($sheet, $column + 6, $row, !$concept->getLearningOutcomes()->isEmpty());
      $this->setCellBooleanValue($sheet, $column + 7, $row, $concept->getHowTo()->hasData());
      $this->setCellBooleanValue($sheet, $column + 8, $row, $concept->getSelfAssessment()->hasData());
      $this->setCellBooleanValue($sheet, $column + 9, $row, !$concept->getExternalResources()->isEmpty());
      $this->setCellValue($sheet, $column + 10, $row, $concept->getIncomingRelations()->count() + $concept->getOutgoingRelations()->count());

      $lastEditInfo = $concept->getLastEditInfo();
      $this->setCellDateTime($sheet, $column + 11, $row, $lastEditInfo[0]);
      $this->setCellValue($sheet, $column + 12, $row, $lastEditInfo[1]);
    }

    $sheet->getStyleByColumnAndRow(1, 1, $column + 12, $row)
        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
  }
}
