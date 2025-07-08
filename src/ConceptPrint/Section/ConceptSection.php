<?php

namespace App\ConceptPrint\Section;

use App\Entity\Concept;
use App\Naming\NamingService;
use App\Router\LtbRouter;
use Bobv\LatexBundle\Exception\LatexException;
use Bobv\LatexBundle\Latex\Element\CustomCommand;
use Bobv\LatexBundle\Latex\Element\Text;
use Pandoc\PandocException;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;

class ConceptSection extends LtbSection
{
  /**
   * Concept constructor.
   *
   * @throws LatexException
   * @throws PandocException
   */
  public function __construct(
    Concept $concept, LtbRouter $router, TranslatorInterface $translator, NamingService $namingService, string $projectDir)
  {
    parent::__construct($concept->getName(), $router, $projectDir);

    // Use sloppy to improve text breaks
    $this->addElement(new CustomCommand('\\sloppy'));

    $this->addElement(new Text(sprintf('\href{%s}{%s}',
      $this->router->generateBrowserUrl('app_concept_show', ['concept' => $concept->getId()]),
      $translator->trans('concept.online-source')
    )));

    // Add concept data
    if ($concept->getDefinition() != '') {
      $this->addElement(new CustomCommand('\\\\'));
      $this->addElement(new Text($concept->getDefinition()));
    }
    $fieldNames = $namingService->get()->concept();
    if ($concept->getIntroduction()->hasData()) {
      $this->addSection($fieldNames->introduction(), $concept->getIntroduction()->getText());
    }
    if ($concept->getTheoryExplanation()->hasData()) {
      $this->addSection($fieldNames->theoryExplanation(), $concept->getTheoryExplanation()->getText());
    }
    if ($concept->getHowTo()->hasData()) {
      $this->addSection($fieldNames->howTo(), $concept->getHowTo()->getText());
    }
    if ($concept->getExamples()->hasData()) {
      $this->addSection($fieldNames->examples(), $concept->getExamples()->getText());
    }

    // Undo sloppy
    $this->addElement(new CustomCommand('\\fussy'));
  }
}
