<?php

namespace App\Excel;

use App\Entity\StudyArea;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class StudyAreaStatusBuilder
 *
 * This class is used to build a Excel sheet with the current study area status
 */
class StudyAreaStatusBuilder
{
  /** @var TranslatorInterface */
  private $translator;

  /** @var Spreadsheet */
  private $spreadsheet;

  /** @var StudyArea */
  private $studyArea;

  /**
   * StudyAreaStatusBuilder constructor.
   *
   * @param TranslatorInterface $translator
   */
  public function __construct(TranslatorInterface $translator)
  {
    $this->translator = $translator;
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

    // Create spreadsheet
    $this->spreadsheet = new Spreadsheet();
    $this->spreadsheet->getProperties()->setCreator($this->studyArea->getOwner()->getDisplayName())
        ->setTitle($this->studyArea->getName())
        ->setSubject($this->translator->trans('excel.subject', ['%item%' => $this->studyArea->getName()]))
        ->setDescription($this->translator->trans('excel.description', ['%item%' => $this->studyArea->getName()]));

    // Create content
    $this->spreadsheet->removeSheetByIndex(0);
    $this->addGeneralInfoSheet();

    // Create writer
    $writer   = new Xlsx($this->spreadsheet);
    $response = new StreamedResponse(
        function () use ($writer) {
          $writer->save('php://output');
        });
    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
    $response->headers->set('Content-Disposition', 'attachment;filename="' . str_replace(' ', '_', strtolower($this->studyArea->getName())) . '_status.xlsx"');

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

    // Todo last edit information
//    $this->setCellTranslatedValue($sheet, $column, $row, 'excel.sheet.general-info.last-edit', true);
  }
}
