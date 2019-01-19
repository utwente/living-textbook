<?php

namespace App\Export\Provider;

use App\Entity\ConceptRelation;
use App\Entity\StudyArea;
use App\Export\ExportService;
use App\Export\ProviderInterface;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\RelationTypeRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RelationProvider implements ProviderInterface
{
  /** @var ConceptRepository */
  private $conceptRepository;
  /** @var ConceptRelationRepository */
  private $conceptRelationRepository;
  /** @var RelationTypeRepository */
  private $relationTypeRepository;

  public function __construct(ConceptRepository $conceptRepository, ConceptRelationRepository $conceptRelationRepository,
                              RelationTypeRepository $relationTypeRepository)
  {
    $this->conceptRepository         = $conceptRepository;
    $this->conceptRelationRepository = $conceptRelationRepository;
    $this->relationTypeRepository    = $relationTypeRepository;
  }

  /**
   * @inheritdoc
   */
  public function getName(): string
  {
    return "relation";
  }

  /**
   * @inheritdoc
   */
  public function getPreview(): string
  {
    return <<<'EOT'
"From";"From name";"To";"To name";"Relation"
"<id>";"<concept-name>";"<id>";"<concept-name>";"<relation>"
"<id>";"<concept-name>";"<id>";"<concept-name>";"<relation>"
"<id>";"<concept-name>";"<id>";"<concept-name>";"<relation>"
EOT;
  }

  /**
   * @inheritdoc
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  public function export(StudyArea $studyArea): Response
  {
    /** @noinspection PhpUnusedLocalVariableInspection Retrieve the relation types as cache */
    $relationTypes = $this->relationTypeRepository->findBy(['studyArea' => $studyArea]);

    // Retrieve the concepts
    $concepts = $this->conceptRepository->findForStudyAreaOrderedByName($studyArea);
    $links    = $this->conceptRelationRepository->findByConcepts($concepts);

    // Sort them first of source name, than on target name
    usort($links, function (ConceptRelation $a, ConceptRelation $b) {
      if ($a->getSourceId() === $b->getSourceId()) {
        return $a->getTarget()->getName() <=> $b->getTarget()->getName();
      }

      return $a->getSource()->getName() <=> $b->getSource()->getName();
    });

    $row         = 1;
    $column      = 1;
    $spreadSheet = new Spreadsheet();
    $sheet       = $spreadSheet->getSheet(0);
    $sheet->setCellValueByColumnAndRow($column++, $row, "From");
    $sheet->setCellValueByColumnAndRow($column++, $row, "From name");
    $sheet->setCellValueByColumnAndRow($column++, $row, "To");
    $sheet->setCellValueByColumnAndRow($column++, $row, "To name");
    $sheet->setCellValueByColumnAndRow($column, $row++, "Relation");
    foreach ($links as $link) {
      $column = 1;
      $sheet->setCellValueByColumnAndRow($column++, $row, $link->getSourceId());
      $sheet->setCellValueByColumnAndRow($column++, $row, $link->getSource()->getName());
      $sheet->setCellValueByColumnAndRow($column++, $row, $link->getTargetId());
      $sheet->setCellValueByColumnAndRow($column++, $row, $link->getTarget()->getName());
      $sheet->setCellValueByColumnAndRow($column, $row++, $link->getRelationName());
    }

    $writer = (new Csv($spreadSheet))
        ->setDelimiter(';')
        ->setUseBOM(true)
        ->setSheetIndex(0);

    $response = new StreamedResponse(function () use ($writer) {
      $writer->save('php://output');
    });
    $response->headers->set('Content-Type', 'application/csv; charset=utf-8');
    ExportService::contentDisposition($response, sprintf('%s_concept_relation_export.csv', $studyArea->getName()));

    return $response;
  }
}
