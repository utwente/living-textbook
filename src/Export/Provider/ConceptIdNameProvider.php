<?php

namespace App\Export\Provider;

use App\Entity\Concept;
use App\Entity\StudyArea;
use App\Export\ExportService;
use App\Export\ProviderInterface;
use App\Repository\ConceptRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConceptIdNameProvider implements ProviderInterface
{

  /** @var ConceptRepository */
  private $conceptRepository;

  public function __construct(ConceptRepository $conceptRepository)
  {
    $this->conceptRepository = $conceptRepository;
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
   * @throws \PhpOffice\PhpSpreadsheet\Exception
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

    $writer = (new Csv($spreadSheet))
        ->setDelimiter(';')
        ->setUseBOM(true)
        ->setSheetIndex(0);

    $response = new StreamedResponse(function () use ($writer) {
      $writer->save('php://output');
    });
    $response->headers->set('Content-Type', 'application/csv; charset=utf-8');
    ExportService::contentDisposition($response, sprintf('%s_concept_id_name_export.csv', $studyArea->getName()));

    return $response;
  }
}
