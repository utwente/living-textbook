<?php

namespace App\ConceptPrint\Section;

use App\Entity\Concept;
use App\Router\LtbRouter;
use BobV\LatexBundle\Exception\LatexException;
use BobV\LatexBundle\Latex\Element\CustomCommand;
use BobV\LatexBundle\Latex\Element\Text;
use Pandoc\PandocException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConceptSection extends LtbSection
{

  /**
   * Concept constructor.
   *
   * @param Concept             $concept
   * @param LtbRouter           $router
   * @param TranslatorInterface $translator
   * @param string              $projectDir
   *
   * @throws LatexException
   * @throws PandocException
   */
  public function __construct(Concept $concept, LtbRouter $router, TranslatorInterface $translator, string $projectDir)
  {

    parent::__construct($concept->getName(), $router, $translator, $projectDir);

    // Use sloppy to improve text breaks
    $this->addElement(new CustomCommand('\\sloppy'));

    $this->addElement(new Text(sprintf('\href{%s}{%s}',
        $this->router->generateBrowserUrl('app_concept_show', ['concept' => $concept->getId()]),
        $this->translator->trans('concept.online-source')
    )));

    // Add concept data
    if ($concept->getDefinition() != '') {
      $this->addElement(new CustomCommand('\\\\'));
      $this->addElement(new Text($concept->getDefinition()));
    }
    if ($concept->getIntroduction()->hasData()) {
      $this->addSection('concept.introduction', $concept->getIntroduction()->getText());
    }
    if ($concept->getTheoryExplanation()->hasData()) {
      $this->addSection('concept.theory-explanation', $concept->getTheoryExplanation()->getText());
    }
    if ($concept->getHowTo()->hasData()) {
      $this->addSection('concept.how-to', $concept->getHowTo()->getText());
    }
    if ($concept->getExamples()->hasData()) {
      $this->addSection('concept.examples', $concept->getExamples()->getText());
    }

    // Undo sloppy
    $this->addElement(new CustomCommand('\\fussy'));
  }
}
