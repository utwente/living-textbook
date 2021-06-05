<?php

namespace App\Export\Provider;

use App\Entity\ConceptRelation;
use App\Entity\StudyArea;
use App\Excel\SpreadsheetHelper;
use App\Export\ProviderInterface;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\RelationTypeRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Response;

class RelationProvider implements ProviderInterface
{
  /** @var ConceptRepository */
  private $conceptRepository;
  /** @var ConceptRelationRepository */
  private $conceptRelationRepository;
  /** @var RelationTypeRepository */
  private $relationTypeRepository;
  /** @var SpreadsheetHelper */
  private $spreadsheetHelper;

  public function __construct(ConceptRepository $conceptRepository, ConceptRelationRepository $conceptRelationRepository,
                              RelationTypeRepository $relationTypeRepository, SpreadsheetHelper $spreadsheetHelper)
  {
    $this->conceptRepository         = $conceptRepository;
    $this->conceptRelationRepository = $conceptRelationRepository;
    $this->relationTypeRepository    = $relationTypeRepository;
    $this->spreadsheetHelper         = $spreadsheetHelper;
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

  public function getSpreadsheet(StudyArea $studyArea): Spreadsheet
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

    return $spreadSheet;
  }

  /**
   * @inheritdoc
   */
  public function export(StudyArea $studyArea): Response
  {
    return $this->spreadsheetHelper->createCsvResponse($this->getSpreadsheet($studyArea),
        sprintf('%s_concept_relation_export.csv', $studyArea->getName()));
  }
}
