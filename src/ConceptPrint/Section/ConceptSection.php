<?php

namespace App\ConceptPrint\Section;

use App\Entity\Concept;
use BobV\LatexBundle\Latex\Element\CustomCommand;
use BobV\LatexBundle\Latex\Section\Section;
use BobV\LatexBundle\Latex\Section\SubSection;
use Pandoc\Pandoc;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConceptSection extends Section
{

  /** @var Pandoc */
  private $pandoc;

  /** @var TranslatorInterface */
  private $translator;

  /** @var KernelInterface */
  private $kernel;

  /**
   * Concept constructor.
   *
   * @param Concept             $concept
   * @param TranslatorInterface $translator
   * @param KernelInterface     $kernel
   *
   * @throws \BobV\LatexBundle\Exception\LatexException
   * @throws \Pandoc\PandocException
   */
  public function __construct(Concept $concept, TranslatorInterface $translator, KernelInterface $kernel)
  {
    $this->pandoc     = new Pandoc();
    $this->translator = $translator;
    $this->kernel     = $kernel;

    parent::__construct($concept->getName());
    $this->setParam('newpage', false);

    // Add concept data
    if ($concept->getIntroduction()->hasData()) {
      $this->addElement(new CustomCommand($this->convertToLatex($concept->getIntroduction()->getText())));
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
    $latex = preg_replace('/(\/uploads\/studyarea\/)/ui', sprintf('%s%spublic$1', $this->kernel->getProjectDir(), DIRECTORY_SEPARATOR), $latex);

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
