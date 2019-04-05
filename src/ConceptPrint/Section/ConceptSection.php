<?php

namespace App\ConceptPrint\Section;

use App\Entity\Concept;
use BobV\LatexBundle\Exception\LatexException;
use BobV\LatexBundle\Latex\Element\CustomCommand;
use BobV\LatexBundle\Latex\Element\Text;
use Pandoc\PandocException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConceptSection extends LtbSection
{

  /**
   * Concept constructor.
   *
   * @param Concept             $learningPath
   * @param RouterInterface     $router
   * @param TranslatorInterface $translator
   * @param string              $projectDir
   *
   * @throws LatexException
   * @throws PandocException
   */
  public function __construct(Concept $learningPath, RouterInterface $router, TranslatorInterface $translator, string $projectDir)
  {

    parent::__construct($learningPath->getName(), $router, $translator, $projectDir);

    $this->addElement(new Text(sprintf('\href{%s}{%s}',
        $this->router->generate('app_concept_show', ['concept' => $learningPath->getId()], RouterInterface::ABSOLUTE_URL),
        $this->translator->trans('concept.online-source')
    )));

    // Add concept data
    if ($learningPath->getDefinition() != '') {
      $this->addElement(new CustomCommand('\\\\'));
      $this->addElement(new Text($learningPath->getDefinition()));
    }
    if ($learningPath->getIntroduction()->hasData()) {
      $this->addSection('concept.introduction', $learningPath->getIntroduction()->getText());
    }
    if ($learningPath->getTheoryExplanation()->hasData()) {
      $this->addSection('concept.theory-explanation', $learningPath->getTheoryExplanation()->getText());
    }
    if ($learningPath->getHowTo()->hasData()) {
      $this->addSection('concept.how-to', $learningPath->getHowTo()->getText());
    }
    if ($learningPath->getExamples()->hasData()) {
      $this->addSection('concept.examples', $learningPath->getExamples()->getText());
    }
  }
}
