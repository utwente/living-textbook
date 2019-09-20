<?php

namespace App\Excel;

use App\Entity\PageLoad;
use App\Entity\StudyArea;
use App\Entity\TrackingEvent;
use App\Repository\PageLoadRepository;
use App\Repository\TrackingEventRepository;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
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

  /** @var TrackingEventRepository */
  private $trackingEventRepository;

  /**
   * TrackingExportBuilder constructor.
   *
   * @param TranslatorInterface     $translator
   * @param SpreadsheetHelper       $spreadsheetHelper
   * @param PageLoadRepository      $pageLoadRepository
   * @param TrackingEventRepository $trackingEventRepository
   */
  public function __construct(
      TranslatorInterface $translator, SpreadsheetHelper $spreadsheetHelper, PageLoadRepository $pageLoadRepository,
      TrackingEventRepository $trackingEventRepository)
  {
    $this->translator              = $translator;
    $this->spreadsheetHelper       = $spreadsheetHelper;
    $this->pageLoadRepository      = $pageLoadRepository;
    $this->trackingEventRepository = $trackingEventRepository;
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

    // Add the exports

    $this->exportPageLoads($studyArea, $spreadsheet->setActiveSheetIndex(0));
    $spreadsheet->createSheet(1);
    $this->exportEvents($studyArea, $spreadsheet->setActiveSheetIndex(1));
    $spreadsheet->setActiveSheetIndex(0);

    // Create response
    return $this->spreadsheetHelper->createExcelResponse($spreadsheet,
        sprintf('%s_tracking_export.xlsx', $studyArea->getName()));
  }

  /**
   * @param StudyArea $studyArea
   * @param Worksheet $sheet
   */
  private function exportPageLoads(StudyArea $studyArea, Worksheet $sheet): void
  {
    // Location
    $column = 0;
    $row    = 1;

    // Create header
    $this->setCommonHeader($sheet, $column, $row, 'Page loads');

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

      // Set common header values
      $this->setCommonHeaderValues($sheet, $column, $row, $pageload);

      // Set extra header values
      $this->spreadsheetHelper->setCellValue($sheet, ++$column, $row, $pageload->getOrigin());
      $this->spreadsheetHelper->setCellValue($sheet, ++$column, $row, $pageload->getPath());

      // Map context items to spreadsheet
      $this->mapContextElements($sheet, $column, $row, $pageload->getPathContext(), $contextMap);
    }
  }


  /**
   * @param StudyArea $studyArea
   * @param Worksheet $sheet
   */
  private function exportEvents(StudyArea $studyArea, Worksheet $sheet): void
  {
    // Location
    $column = 0;
    $row    = 1;

    // Create header
    $this->setCommonHeader($sheet, $column, $row, 'Events');

    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.event', true);

    // Context key map, predefine commonly used values
    $contextMap = [];

    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.concept', true);
    $contextMap['conceptid'] = $column;

    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.learning-path', true);
    $contextMap['learningpathid'] = $column;

    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.link', true);
    $contextMap['link'] = $column;

    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.link-blank', true);
    $contextMap['blank'] = $column;

    $contextMap['__next'] = ++$column;

    // Create content
    $trackingEvents = $this->trackingEventRepository->getByStudyAreaOrderedOnIds($studyArea);
    foreach ($trackingEvents as $trackingEvent) {
      $row++;
      $column = 0;

      // Set common header values
      $this->setCommonHeaderValues($sheet, $column, $row, $trackingEvent);

      // Set extra header values
      $this->spreadsheetHelper->setCellValue($sheet, ++$column, $row, $trackingEvent->getEvent());

      // Map context items to spreadsheet
      $this->mapContextElements($sheet, $column, $row, $trackingEvent->getContext(), $contextMap);
    }
  }

  /**
   * @param Worksheet            $sheet
   * @param iterable|null        $context
   * @param array                $contextMap
   * @param int                  $row
   * @param                      $column
   */
  private function mapContextElements(Worksheet &$sheet, int &$column, int $row, ?iterable $context, array $contextMap): void
  {
    foreach ($context ?? [] as $cKey => $cItem) {
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

  /**
   * @param Worksheet $sheet
   * @param int       $column
   * @param int       $row
   * @param string    $title
   */
  private function setCommonHeader(Worksheet &$sheet, int &$column, int $row, string $title): void
  {
    $sheet->setTitle($title);

    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.user-id', true);
    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.session-id', true);
    $sheet->getColumnDimensionByColumn(++$column)->setAutoSize(true);
    $this->spreadsheetHelper->setCellTranslatedValue($sheet, $column, $row, 'tracking.export.timestamp', true);
  }

  /**
   * @param Worksheet $sheet
   * @param int       $column
   * @param int       $row
   * @param object    $object
   */
  private function setCommonHeaderValues(Worksheet &$sheet, int &$column, int $row, $object): void
  {
    assert($object instanceof PageLoad || $object instanceof TrackingEvent);

    $this->spreadsheetHelper->setCellValue($sheet, ++$column, $row, $object->getUserId());
    $this->spreadsheetHelper->setCellValue($sheet, ++$column, $row, $object->getSessionId());
    $this->spreadsheetHelper->setCellDateTime($sheet, ++$column, $row, $object->getTimestamp(), true);
  }

}
