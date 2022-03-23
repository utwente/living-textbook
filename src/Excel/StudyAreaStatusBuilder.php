<?php

namespace App\Excel;

use App\Entity\Concept;
use App\Entity\RelationType;
use App\Entity\StudyArea;
use App\Naming\NamingService;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\RelationTypeRepository;
use Doctrine\Common\Collections\Collection;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class StudyAreaStatusBuilder.
 *
 * This class is used to build a Excel sheet with the current study area status
 */
class StudyAreaStatusBuilder
{
  /** @var NamingService */
  private $namingService;

  /** @var TranslatorInterface */
  private $translator;

  /** @var RelationTypeRepository */
  private $relationTypeRepo;

  /** @var ConceptRepository */
  private $conceptRepo;

  /** @var ConceptRelationRepository */
  private $conceptRelationRepo;

  /** @var SpreadsheetHelper */
  private $spreadsheetHelper;

  /** @var Spreadsheet */
  private $spreadsheet;

  /** @var StudyArea */
  private $studyArea;

  /** @var RelationType[]|Collection */
  private $relationTypes;

  /** @var Concept[]|Collection */
  private $concepts;

  /** StudyAreaStatusBuilder constructor. */
  public function __construct(TranslatorInterface $translator, ConceptRepository $conceptRepo,
                              ConceptRelationRepository $conceptRelationRepo, RelationTypeRepository $relationTypeRepo,
                              SpreadsheetHelper $spreadsheetHelper, NamingService $namingService)
  {
    $this->translator          = $translator;
    $this->conceptRelationRepo = $conceptRelationRepo;
    $this->conceptRepo         = $conceptRepo;
    $this->relationTypeRepo    = $relationTypeRepo;
    $this->spreadsheetHelper   = $spreadsheetHelper;
    $this->namingService       = $namingService;
  }

  /**
   * Create the excel response.
   *
   * @throws Exception
   */
  public function build(StudyArea $studyArea): Response
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

    return $this->spreadsheetHelper->createExcelResponse($this->spreadsheet,
        sprintf('%s_status.xlsx', $studyArea->getName()));
  }

  /** @throws Exception */
  private function addGeneralInfoSheet()
  {
    $sheet = $this->spreadsheetHelper->createSheet($this->spreadsheet, 'excel.sheet.general-info._tab');

    $column = 1;
    $row    = 1;

    $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
    $sheet->getColumnDimensionByColumn($column + 1)->setAutoSize(true);
    $sheet->getColumnDimensionByColumn($column + 2)->setAutoSize(true);

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-info.name', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 1, $row, $this->studyArea->getName());
    $row++;

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-info.owner', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 1, $row, $this->studyArea->getOwner()->getDisplayName());
    $row++;

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-info.access-type', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 1, $row, ucfirst($this->studyArea->getAccessType()));
    $row++;

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-info.creation-data', true);
    $this->spreadsheetHelper->setCellDateTime($sheet, $column + 1, $row, $this->studyArea->getCreatedAt(), true);
    $row++;

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-info.last-edit', true);
    $lastEditInfo = $this->studyArea->getLastEditInfo();
    $this->spreadsheetHelper->setCellDateTime($sheet, $column + 1, $row, $lastEditInfo[0], true);
    $this->spreadsheetHelper->setCellValue($sheet, $column + 2, $row, $lastEditInfo[1]);

    $sheet->getStyleByColumnAndRow(1, 1, $column + 2, $row)
        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
  }

  /** @throws Exception */
  private function addGeneralConceptStatisticsSheet()
  {
    $sheet = $this->spreadsheetHelper->createSheet($this->spreadsheet, 'excel.sheet.general-concept-statistics._tab');

    $column = 1;
    $row    = 1;

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.statistics-item', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 1, $row, 'excel.sheet.general-concept-statistics.total-concepts', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 2, $row, 'excel.sheet.general-concept-statistics.no-text', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 3, $row, 'excel.sheet.general-concept-statistics.no-definition', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 4, $row, 'excel.sheet.general-concept-statistics.no-introduction', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 5, $row, 'excel.sheet.general-concept-statistics.no-prior-knowledge', true);
    $this->spreadsheetHelper->setCellValue($sheet, $column + 6, $row,
        $this->translator->trans('excel.sheet.general-concept-statistics.no-learning-outcomes', [
            '%plural%' => $this->namingService->get()->learningOutcome()->objs(),
        ]), true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 7, $row, 'excel.sheet.general-concept-statistics.no-relations', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 8, $row, 'excel.sheet.general-concept-statistics.more-relations-5', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 9, $row, 'excel.sheet.general-concept-statistics.more-relations-10', true);

    $row++;

    $column = 2;
    $counts = array_fill(0, 9, 0);
    $setter = function (Concept $concept, array &$counts, int $index, bool $condition) use (&$sheet, $column, $row) {
      if ($condition) {
        $this->spreadsheetHelper->setCellValue($sheet, $column + $index, $row + 1 + $counts[$index], $concept->getName());
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
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.count', true);
    foreach ($counts as $key => $count) {
      $this->spreadsheetHelper->setCellValue($sheet, $column + 1 + $key, $row, $count);
    }

    for ($column = 1; $column <= 10; $column++) {
      $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
    }
  }

  /** @throws Exception */
  private function addGeneralRelationshipStatisticsSheet()
  {
    $sheet = $this->spreadsheetHelper->createSheet($this->spreadsheet, 'excel.sheet.general-relationship-statistics._tab');

    $column = 1;
    $row    = 1;

    $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
    $sheet->getColumnDimensionByColumn($column + 1)->setAutoSize(true);

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-relationship-statistics.relationships', true);
    $row++;

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.statistics-item', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 1, $row, 'excel.sheet.general-relationship-statistics.types-number', true);
    $row++;

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-relationship-statistics.types');
    $this->spreadsheetHelper->setCellValue($sheet, $column + 1, $row, count($this->relationTypes));
    $row++;

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.statistics-item', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 1, $row, 'excel.sheet.general-relationship-statistics.number', true);
    $row++;

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-relationship-statistics.number-per-type');

    foreach ($this->relationTypes as $relationType) {
      $row++;
      $this->spreadsheetHelper->setCellValue($sheet, $column, $row, sprintf('  %s',
          $this->translator->trans('excel.sheet.general-relationship-statistics.type', ['%type%' => $relationType->getName()])));
      $this->spreadsheetHelper->setCellValue($sheet, $column + 1, $row, $this->conceptRelationRepo->getByRelationTypeCount($relationType));
    }

    $sheet->getStyleByColumnAndRow(1, 1, $column + 1, $row)
        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyleByColumnAndRow(1, 1)->getBorders()->getRight()->setBorderStyle(Border::BORDER_NONE);
    $sheet->getStyleByColumnAndRow(2, 1)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_NONE);
  }

  /** @throws Exception */
  private function addDetailedRelationshipsOverviewSheet()
  {
    $sheet = $this->spreadsheetHelper->createSheet($this->spreadsheet, 'excel.sheet.detailed-relationships-overview._tab');

    $column = 1;
    $row    = 1;

    // Add two to count for offset
    $conceptCount = count($this->concepts) + 2;

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.concept', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row + $conceptCount, 'excel.concept', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 1, $row, 'excel.outgoing-relations', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 1, $row + $conceptCount, 'excel.incoming-relations', true);

    $maxCol = 1;
    foreach ($this->concepts as $concept) {
      $row++;
      $column = 1;

      $this->spreadsheetHelper->setCellValue($sheet, $column, $row, $concept->getName());
      $this->spreadsheetHelper->setCellValue($sheet, $column, $row + $conceptCount, $concept->getName());

      foreach ($concept->getOutgoingRelations() as $conceptRelation) {
        $column++;
        $this->spreadsheetHelper->setCellValue($sheet, $column, $row,
            sprintf('* %s %s', $conceptRelation->getRelationName(), $conceptRelation->getTarget()->getName()));
        if ($column > $maxCol) {
          $maxCol = $column;
        }
      }

      $column = 1;
      foreach ($concept->getIncomingRelations() as $conceptRelation) {
        $column++;
        $this->spreadsheetHelper->setCellValue($sheet, $column, $row + $conceptCount,
            sprintf('%s %s *', $conceptRelation->getSource()->getName(), $conceptRelation->getRelationName()));
        if ($column > $maxCol) {
          $maxCol = $column;
        }
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

  /** @throws Exception */
  private function addDetailedConceptOverviewSheet()
  {
    $sheet = $this->spreadsheetHelper->createSheet($this->spreadsheet, 'excel.sheet.detailed-concept-overview._tab');

    for ($column = 1; $column <= 13; $column++) {
      $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
    }

    $column = 1;
    $row    = 1;

    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.detailed-concept-overview.concept-name', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 1, $row, 'excel.sheet.detailed-concept-overview.definition', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 2, $row, 'excel.sheet.detailed-concept-overview.introduction', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 3, $row, 'excel.sheet.detailed-concept-overview.explanation', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 4, $row, 'excel.sheet.detailed-concept-overview.prior-knowledge', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 5, $row, 'excel.sheet.detailed-concept-overview.examples', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 6, $row, ucfirst($this->namingService->get()->learningOutcome()->objs()), true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 7, $row, 'excel.sheet.detailed-concept-overview.how-to', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 8, $row, 'excel.sheet.detailed-concept-overview.self-assessment', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 9, $row, 'excel.sheet.detailed-concept-overview.external-links', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 10, $row, 'excel.sheet.detailed-concept-overview.number-of-relations', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 11, $row, 'excel.sheet.detailed-concept-overview.last-edit-time', true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column + 12, $row, 'excel.sheet.detailed-concept-overview.last-editor', true);

    foreach ($this->concepts as $concept) {
      $row++;
      $this->spreadsheetHelper->setCellValue($sheet, $column, $row, $concept->getName());
      $this->spreadsheetHelper->setCellBooleanValue($sheet, $column + 1, $row, $concept->getDefinition() != '');
      $this->spreadsheetHelper->setCellBooleanValue($sheet, $column + 2, $row, $concept->getIntroduction()->hasData());
      $this->spreadsheetHelper->setCellBooleanValue($sheet, $column + 3, $row, $concept->getTheoryExplanation()->hasData());
      $this->spreadsheetHelper->setCellBooleanValue($sheet, $column + 4, $row, !$concept->getPriorKnowledge()->isEmpty());
      $this->spreadsheetHelper->setCellBooleanValue($sheet, $column + 5, $row, $concept->getExamples()->hasData());
      $this->spreadsheetHelper->setCellBooleanValue($sheet, $column + 6, $row, !$concept->getLearningOutcomes()->isEmpty());
      $this->spreadsheetHelper->setCellBooleanValue($sheet, $column + 7, $row, $concept->getHowTo()->hasData());
      $this->spreadsheetHelper->setCellBooleanValue($sheet, $column + 8, $row, $concept->getSelfAssessment()->hasData());
      $this->spreadsheetHelper->setCellBooleanValue($sheet, $column + 9, $row, !$concept->getExternalResources()->isEmpty());
      $this->spreadsheetHelper->setCellValue($sheet, $column + 10, $row, $concept->getIncomingRelations()->count() + $concept->getOutgoingRelations()->count());

      $lastEditInfo = $concept->getLastEditInfo();
      $this->spreadsheetHelper->setCellDateTime($sheet, $column + 11, $row, $lastEditInfo[0]);
      $this->spreadsheetHelper->setCellValue($sheet, $column + 12, $row, $lastEditInfo[1]);
    }

    $sheet->getStyleByColumnAndRow(1, 1, $column + 12, $row)
        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
  }
}
