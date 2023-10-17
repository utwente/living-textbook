<?php

namespace App\Export\Provider;

use App\Entity\Concept;
use App\Entity\StudyArea;
use App\Excel\SpreadsheetHelper;
use App\Export\ProviderInterface;
use App\Repository\ConceptRepository;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Response;

class ConceptIdNameProvider implements ProviderInterface
{
  private ConceptRepository $conceptRepository;

  private SpreadsheetHelper $spreadsheetHelper;

  public function __construct(ConceptRepository $conceptRepository, SpreadsheetHelper $spreadsheetHelper)
  {
    $this->conceptRepository = $conceptRepository;
    $this->spreadsheetHelper = $spreadsheetHelper;
  }

  public function getName(): string
  {
    return 'concept-id-name';
  }

  public function getPreview(): string
  {
    return <<<'EOT'
"<id>";"<concept-name>"
"<id>";"<concept-name>"
"<id>";"<concept-name>"
EOT;
  }

  /**
   * Get the export spreadsheet.
   *
   * @throws Exception
   */
  public function getSpreadSheet(StudyArea $studyArea): Spreadsheet
  {
    /** @var Concept[] $concepts */
    $concepts = $this->conceptRepository
      ->findForStudyAreaOrderByNameQb($studyArea)
      ->orderBy('c.id')
      ->getQuery()->getResult();

    $row         = 1;
    $spreadSheet = new Spreadsheet();
    $sheet       = $spreadSheet->getSheet(0);
    foreach ($concepts as $concept) {
      $column = 1;
      $sheet->setCellValue(CellAddress::fromColumnAndRow($column++, $row), $concept->getId());
      $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row++), $concept->getName());
    }

    return $spreadSheet;
  }

  /** @throws Exception */
  public function export(StudyArea $studyArea): Response
  {
    return $this->spreadsheetHelper->createCsvResponse($this->getSpreadSheet($studyArea),
      sprintf('%s_concept_id_name_export.csv', $studyArea->getName()));
  }
}
