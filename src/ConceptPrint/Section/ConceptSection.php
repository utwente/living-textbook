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
   * @param string $html
   *
   * @return string
   * @throws \Pandoc\PandocException
   */
  private function convertHtmlToLatex(string $html)
  {
    // Try to replace latex equations
    $latexImages  = [];
    $normalImages = [];

    // Load DOM, but ignore libxml errors triggered by HTML5
    $dom = new \DOMDocument();
    libxml_clear_errors();
    libxml_use_internal_errors(true);
    if ($dom->loadHTML($html)) {
      $figures = $dom->getElementsByTagName('figure');
      foreach ($figures as $figure) {
        /** @var \DOMElement $figure */
        if (!$figure->hasAttribute('class')) continue;

        // Check for class
        $classes = explode(' ', $figure->getAttribute('class'));
        $isLatex = in_array('latex-figure', $classes);
        $isImage = in_array('image', $classes);
        if (!$isLatex && !$isImage) continue;

        // Retrieve inner tags
        $img     = $figure->getElementsByTagName('img');
        $caption = $figure->getElementsByTagName('figcaption');

        // Check tag attributes
        if (count($img) < 1 || count($caption) < 1) continue;
        if (count($caption->item(0)->childNodes) < 1) continue;

        // Retrieve nodes
        $imgNode     = $img->item(0);
        $captionNode = $caption->item(0)->childNodes->item(0);

        // Retrieve information
        $id      = md5($dom->saveHTML($imgNode));
        $caption = $dom->saveHTML($captionNode);

        if ($isLatex) {
          if (!$imgNode->hasAttribute('alt')) continue;

          // Retrieve relevant information
          $latex            = $imgNode->getAttribute('alt');
          $latexImages[$id] = [
              'replace' => urldecode($latex),
              'caption' => $caption,
          ];
        } else if ($isImage) {
          if (!$imgNode->hasAttribute('src')) continue;

          // Retrieve relevant information
          $image             = $imgNode->getAttribute('src');
          $normalImages[$id] = [
              'replace' => preg_replace('/(\/uploads\/studyarea\/)/ui', sprintf('%s%spublic$1', $this->projectDir, DIRECTORY_SEPARATOR), $image),
              'caption' => $caption,
          ];
        }

        // Place the placeholder
        $figure->parentNode->replaceChild($dom->createElement('span', sprintf('placeholder-%s', $id)), $figure);
      }
      $html = $dom->saveHTML($dom);
    }

    // Restore errors
    libxml_clear_errors();
    libxml_disable_entity_loader(false);

    $latex = $this->pandoc->convert($html, 'html', 'latex');

    // Replace latex image placeholders with action LaTeX code
    $latex = $this->replacePlaceholder($latex, $latexImages, '\\begin{figure}[!ht]\\begin{displaymath}\boxed{%s}\\end{displaymath}\\caption*{%s}\\end{figure}');
    $latex = $this->replacePlaceholder($latex, $normalImages, '\\begin{figure}[!ht]\\includegraphics[frame]{%s}\\caption*{%s}\\end{figure}');

    return $latex;
  }

  /**
   * @param string $title
   * @param string $html
   *
   * @throws \BobV\LatexBundle\Exception\LatexException
   * @throws \Pandoc\PandocException
   */
  private function addSection(string $title, string $html)
  {
    $this->addElement((new SubSection($title))->addElement(new CustomCommand($this->convertHtmlToLatex($html))));
  }

  private function replacePlaceholder(string $latex, array $replaceInfo, $replacement)
  {
    foreach ($replaceInfo as $id => $toReplace) {
      $new   = sprintf($replacement, $toReplace['replace'], $toReplace['caption']);
      $latex = str_replace(sprintf('{placeholder-%s}', $id), $new, $latex);
    }

    return $latex;
  }
}
