<?php

namespace App\ConceptPrint\Section;

use App\Entity\Concept;
use BobV\LatexBundle\Latex\Element\CustomCommand;
use BobV\LatexBundle\Latex\Element\Text;
use BobV\LatexBundle\Latex\Section\Section;
use BobV\LatexBundle\Latex\Section\SubSection;
use Pandoc\Pandoc;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConceptSection extends Section
{

  /** @var Pandoc */
  private $pandoc;

  /** @var RouterInterface */
  private $router;

  /** @var TranslatorInterface */
  private $translator;

  /** @var string */
  private $projectDir;

  /**
   * Concept constructor.
   *
   * @param Concept             $concept
   * @param RouterInterface     $router
   * @param TranslatorInterface $translator
   * @param string              $projectDir
   *
   * @throws \BobV\LatexBundle\Exception\LatexException
   * @throws \Pandoc\PandocException
   */
  public function __construct(Concept $concept, RouterInterface $router, TranslatorInterface $translator, string $projectDir)
  {
    $this->pandoc     = new Pandoc();
    $this->router     = $router;
    $this->translator = $translator;
    $this->projectDir = $projectDir;

    parent::__construct($concept->getName());
    $this->setParam('newpage', false);

    $this->addElement(new Text(sprintf('\href{%s}{%s}\\\\',
        $this->router->generate('app_concept_show', ['concept' => $concept->getId()], RouterInterface::ABSOLUTE_URL),
        $this->translator->trans('concept.online-source')
    )));

    // Add concept data
    if ($concept->getDefinition() != '') {
      $this->addElement(new Text($concept->getDefinition()));
    }
    if ($concept->getIntroduction()->hasData()) {
      $this->addSection($translator->trans('concept.introduction'), $concept->getIntroduction()->getText());
    }
    if ($concept->getTheoryExplanation()->hasData()) {
      $this->addSection($translator->trans('concept.theory-explanation'), $concept->getTheoryExplanation()->getText());
    }
    if ($concept->getHowTo()->hasData()) {
      $this->addSection($translator->trans('concept.how-to'), $concept->getHowTo()->getText());
    }
    if ($concept->getExamples()->hasData()) {
      $this->addSection($translator->trans('concept.examples'), $concept->getExamples()->getText());
    }
  }

  /**
   * @param string $text
   *
   * @return string
   * @throws \Pandoc\PandocException
   */
  private function convertToLatex(string $text)
  {
    $latex = $this->pandoc->convert($text, 'html', 'latex');

    // Generated latex can contain uploaded images, so we need to detect those and update them to reference the actual path on disk
    $latex = preg_replace('/(\/uploads\/studyarea\/)/ui', sprintf('%s%spublic$1', $this->projectDir, DIRECTORY_SEPARATOR), $latex);

    return $latex;
  }

  /**
   * @param string $title
   * @param        $text
   *
   * @throws \BobV\LatexBundle\Exception\LatexException
   * @throws \Pandoc\PandocException
   */
  private function addSection(string $title, $text)
  {
    $this->addElement((new SubSection($title))->addElement(new CustomCommand($this->convertToLatex($text))));
  }
}
