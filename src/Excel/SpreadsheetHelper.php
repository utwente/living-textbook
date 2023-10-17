<?php

namespace App\Excel;

use App\Export\ExportService;
use DateTime;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
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
  private TranslatorInterface $translator;

  /** SpreadsheetHelper constructor. */
  public function __construct(TranslatorInterface $translator)
  {
    $this->translator = $translator;
  }

  /** Creates an Excel writer. */
  public function createExcelWriter(Spreadsheet $spreadsheet): Xlsx
  {
    return new Xlsx($spreadsheet);
  }

  /**
   * Create an Excel response from a spreadsheet.
   *
   * @return StreamedResponse
   */
  public function createExcelResponse(Spreadsheet $spreadsheet, string $filename)
  {
    // Create writer
    $writer   = $this->createExcelWriter($spreadsheet);
    $response = new StreamedResponse(
      function () use ($writer) {
        $writer->save('php://output');
      });

    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
    ExportService::contentDisposition($response, $filename);

    return $response;
  }

  /** Creates a CSV writer. */
  public function createCsvWriter(Spreadsheet $spreadsheet): Csv
  {
    return (new Csv($spreadsheet))
      ->setDelimiter(';')
      ->setUseBOM(true)
      ->setSheetIndex(0);
  }

  /**
   * Create a CSV response from a spreadsheet.
   *
   * @return StreamedResponse
   */
  public function createCsvResponse(Spreadsheet $spreadsheet, string $filename)
  {
    $writer = $this->createCsvWriter($spreadsheet);

    $response = new StreamedResponse(function () use ($writer) {
      $writer->save('php://output');
    });
    $response->headers->set('Content-Type', 'application/csv; charset=utf-8');
    ExportService::contentDisposition($response, $filename);

    return $response;
  }

  /**
   * Creates a new sheet with the specified name.
   *
   * @throws Exception
   */
  public function createSheet(Spreadsheet $spreadsheet, string $name): Worksheet
  {
    $sheet = new Worksheet($spreadsheet, $this->translator->trans($name));
    $spreadsheet->addSheet($sheet);

    return $sheet;
  }

  public function setCellBooleanValue(Worksheet &$sheet, int $column, int $row, bool $value)
  {
    $this->setCellTranslatedValue($sheet, $column, $row, $value ? 'excel.boolean.yes' : 'excel.boolean.no');
  }

  public function setCellTranslatedValue(Worksheet &$sheet, int $column, int $row, string $value, bool $bold = false)
  {
    $this->setCellValue($sheet, $column, $row, $this->translator->trans($value), $bold);
  }

  public function setCellValue(Worksheet &$sheet, int $column, int $row, mixed $value, bool $bold = false)
  {
    $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), $value);

    if ($bold) {
      $sheet->getStyle(CellAddress::fromColumnAndRow($column, $row))->getFont()->setBold(true);
    }
  }

  public function setCellDateTime(Worksheet &$sheet, int $column, int $row, DateTime $dateTime, bool $leftAligned = false, bool $bold = false)
  {
    $this->setCellValue($sheet, $column, $row, Date::PHPToExcel($dateTime), $bold);
    $sheet->getStyle(CellAddress::fromColumnAndRow($column, $row))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

    if ($leftAligned) {
      $sheet->getStyle(CellAddress::fromColumnAndRow($column, $row))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }
  }
}
