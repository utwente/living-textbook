<?php

namespace App\Excel;

use App\Entity\StudyArea;
use App\Repository\PageLoadRepository;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TrackingExportBuilder
{

  /** @var TranslatorInterface */
  private $translator;

  /** @var SpreadsheetHelper */
  private $spreadsheetHelper;

  /** @var PageLoadRepository */
  private $pageLoadRepository;

  /**
   * TrackingExportBuilder constructor.
   *
   * @param TranslatorInterface $translator
   * @param SpreadsheetHelper   $spreadsheetHelper
   * @param PageLoadRepository  $pageLoadRepository
   */
  public function __construct(TranslatorInterface $translator, SpreadsheetHelper $spreadsheetHelper, PageLoadRepository $pageLoadRepository)
  {
    $this->translator         = $translator;
    $this->spreadsheetHelper  = $spreadsheetHelper;
    $this->pageLoadRepository = $pageLoadRepository;
  }

  /**
   * Create the excel response
   *
   * @param StudyArea $studyArea
   *
   * @return Response
   * @throws Exception
   */
  public function build(StudyArea $studyArea): Response
  {
    // Create spreadsheet
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()->setCreator($studyArea->getOwner()->getDisplayName())
        ->setTitle($studyArea->getName())
        ->setSubject($this->translator->trans('tracking.export.subject', ['%item%' => $studyArea->getName()]))
        ->setDescription($this->translator->trans('tracking.export.description', ['%item%' => $studyArea->getName()]));

    // Location
    $sheet  = $spreadsheet->getActiveSheet();
    $column = 0;
    $row    = 1;

    // Create header
    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.user-id', true);
    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.session-id', true);
    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.timestamp', true);
    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.origin', true);
    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.path', true);

    // Controller argument map, predefine commonly used values
    $contextMap = [
        '_controller' => NULL, // Do not include this item in the export
    ];

    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.route', true);
    $contextMap['_route'] = $column;

    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.study-area', true);
    $contextMap['_studyarea'] = $column;

    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.concept', true);
    $contextMap['concept'] = $column;

    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.learning-path', true);
    $contextMap['learningpath'] = $column;

    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.learning-outcome', true);
    $contextMap['learningoutcome'] = $column;

    $contextMap['__next'] = ++$column;

    // Create content
    $pageloads = $this->pageLoadRepository->getByStudyAreaOrderedOnIds($studyArea);
    foreach ($pageloads as $pageload) {
      $row++;
      $column = 0;

      $this->spreadsheetHelper->setCellValue($sheet, ++$column, $row, $pageload->getUserId());
      $this->spreadsheetHelper->setCellValue($sheet, ++$column, $row, $pageload->getSessionId());
      $this->spreadsheetHelper->setCellDateTime($sheet, ++$column, $row, $pageload->getTimestamp(), true);
      $this->spreadsheetHelper->setCellValue($sheet, ++$column, $row, $pageload->getOrigin());
      $this->spreadsheetHelper->setCellValue($sheet, ++$column, $row, $pageload->getPath());

      // Map context items to spreadsheet
      foreach ($pageload->getPathContext() as $cKey => $cItem) {
        $cKey = strtolower($cKey);

        if (!array_key_exists($cKey, $contextMap)) {
          // Create column
          $contextMap[$cKey] = $contextMap['__next'];
          $contextMap['__next']++;

          $sheet->getColumnDimensionByColumn($contextMap[$cKey])->setAutoSize(true);
          $this->spreadsheetHelper->setCellValue($sheet, $contextMap[$cKey], 1, $cKey, true);
        }

        $column = $contextMap[$cKey];
        if ($column) {
          $this->spreadsheetHelper->setCellValue($sheet, $column, $row, $cItem);
        }
      }
    }

    // Create response
    return $this->spreadsheetHelper->createExcelResponse($spreadsheet,
        sprintf('%s_tracking_export.xlsx', $studyArea->getName()));
  }
}
