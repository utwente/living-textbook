<?php

namespace App\Excel;

use App\Export\ExportService;
use DateTime;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class SpreadsheetHelper
{

  /** @var TranslatorInterface */
  private $translator;

  /**
   * SpreadsheetHelper constructor.
   *
   * @param TranslatorInterface $translator
   */
  public function __construct(TranslatorInterface $translator)
  {
    $this->translator = $translator;
  }

  /**
   * Create an Excel response from a spreadsheet
   *
   * @param Spreadsheet $spreadsheet
   * @param string      $filename
   *
   * @return StreamedResponse
   */
  public function createExcelResponse(Spreadsheet $spreadsheet, string $filename)
  {
    // Create writer
    $writer   = new Xlsx($spreadsheet);
    $response = new StreamedResponse(
        function () use ($writer) {
          $writer->save('php://output');
        });

    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
    ExportService::contentDisposition($response, $filename);

    return $response;
  }

  /**
   * Create a CSV response from a spreadsheet
   *
   * @param Spreadsheet $spreadsheet
   * @param string      $filename
   *
   * @return StreamedResponse
   */
  public function createCsvResponse(Spreadsheet $spreadsheet, string $filename)
  {
    $writer = (new Csv($spreadsheet))
        ->setDelimiter(';')
        ->setUseBOM(true)
        ->setSheetIndex(0);

    $response = new StreamedResponse(function () use ($writer) {
      $writer->save('php://output');
    });
    $response->headers->set('Content-Type', 'application/csv; charset=utf-8');
    ExportService::contentDisposition($response, $filename);

    return $response;
  }

  /**
   * Creates a new sheet with the specified name
   *
   * @param Spreadsheet $spreadsheet
   * @param string      $name
   *
   * @return Worksheet
   *
   * @throws Exception
   */
  public function createSheet(Spreadsheet $spreadsheet, string $name): Worksheet
  {
    $sheet = new Worksheet($spreadsheet, $this->translator->trans($name));
    $spreadsheet->addSheet($sheet);

    return $sheet;
  }

  /**
   * @param Worksheet $sheet
   * @param int       $column
   * @param int       $row
   * @param bool      $value
   */
  public function setCellBooleanValue(Worksheet &$sheet, int $column, int $row, bool $value)
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
  public function setCellTranslatedValue(Worksheet &$sheet, int $column, int $row, string $value, bool $bold = false)
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
  public function setCellValue(Worksheet &$sheet, int $column, int $row, $value, bool $bold = false)
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
   * @param DateTime  $dateTime
   * @param bool      $leftAligned
   * @param bool      $bold
   */
  public function setCellDateTime(Worksheet &$sheet, int $column, int $row, DateTime $dateTime, bool $leftAligned = false, bool $bold = false)
  {
    $this->setCellValue($sheet, $column, $row, Date::PHPToExcel($dateTime), $bold);
    $sheet->getStyleByColumnAndRow($column, $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

    if ($leftAligned) {
      $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }
  }
}
