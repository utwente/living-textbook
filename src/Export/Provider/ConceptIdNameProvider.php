<?php

namespace App\Export\Provider;

use App\Entity\Concept;
use App\Entity\StudyArea;
use App\Excel\SpreadsheetHelper;
use App\Export\ProviderInterface;
use App\Repository\ConceptRepository;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Response;

class ConceptIdNameProvider implements ProviderInterface
{

  /** @var ConceptRepository */
  private $conceptRepository;

  /** @var SpreadsheetHelper */
  private $spreadsheetHelper;

  public function __construct(ConceptRepository $conceptRepository, SpreadsheetHelper $spreadsheetHelper)
  {
    $this->conceptRepository = $conceptRepository;
    $this->spreadsheetHelper = $spreadsheetHelper;
  }

  /**
   * @inheritdoc
   */
  public function getName(): string
  {
    return 'concept-id-name';
  }

  /**
   * @inheritdoc
   */
  public function getPreview(): string
  {
    return <<<'EOT'
"<id>";"<concept-name>"
"<id>";"<concept-name>"
"<id>";"<concept-name>"
EOT;
  }

  /**
   * @inheritdoc
   * @throws Exception
   */
  public function export(StudyArea $studyArea): Response
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
      $sheet->setCellValueByColumnAndRow($column++, $row, $concept->getId());
      $sheet->setCellValueByColumnAndRow($column, $row++, $concept->getName());
    }

    return $this->spreadsheetHelper->createCsvResponse($spreadSheet,
        sprintf('%s_concept_id_name_export.csv', $studyArea->getName()));
  }
}
