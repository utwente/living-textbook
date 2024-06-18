<?php

namespace App\ConceptPrint\Section;

use App\Router\LtbRouter;
use Bobv\LatexBundle\Exception\LatexException;
use Bobv\LatexBundle\Helper\Parser;
use Bobv\LatexBundle\Latex\Element\CustomCommand;
use Bobv\LatexBundle\Latex\Section\Section;
use Bobv\LatexBundle\Latex\Section\SubSection;
use DOMDocument;
use DOMElement;
use Pandoc\Pandoc;
use Pandoc\PandocException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class LtbSection extends Section
{
  protected readonly Pandoc $pandoc; // Set in constructor
  protected readonly Filesystem $fileSystem; // Set in constructor
  protected readonly Parser $parser; // Set in constructor
  protected readonly string $baseUrl; // Set in constructor

  /** @throws LatexException */
  public function __construct(
    string $name,
    protected readonly LtbRouter $router,
    protected readonly string $projectDir)
  {
    $this->pandoc     = new Pandoc($_ENV['PANDOC_PATH']);
    $this->fileSystem = new Filesystem();
    $this->parser     = new Parser();

    // Generate base url
    $this->baseUrl = $router->generate('base_url', [], UrlGeneratorInterface::ABSOLUTE_URL);

    parent::__construct($name);

    // Set new page to false by default
    $this->setParam('newpage', false);
  }

  /**
   * @throws LatexException
   * @throws PandocException
   */
  protected function addSection(string $title, string $html)
  {
    // See https://tex.stackexchange.com/a/282/110054
    $this->addElement(new CustomCommand('\\FloatBarrier'));
    $this->addElement((new SubSection($title))->addElement(new CustomCommand($this->convertHtmlToLatex($html))));
  }

  /**
   * @throws PandocException
   *
   * @return string
   */
  protected function convertHtmlToLatex(string $html)
  {
    // Try to replace latex equations
    $latexImages       = [];
    $inlineLatexImages = [];
    $normalImages      = [];

    // Load DOM, but ignore libxml errors triggered by HTML5
    $dom = new DOMDocument();
    libxml_clear_errors();
    libxml_use_internal_errors(true);
    if ($dom->loadHTML($html)) {
      // We need to extract the figures here, as replacing them in the dom removes them from the
      // original node list, which in turns ensures the loop does not complete
      $extractedFigures = [];
      foreach ($dom->getElementsByTagName('figure') as $figure) {
        $extractedFigures[] = $figure;
      }
      foreach ($dom->getElementsByTagName('span') as $inlineFigure) {
        /** @var DOMElement $inlineFigure */
        if (!$inlineFigure->hasAttribute('class')) {
          continue;
        }
        $classes = explode(' ', $inlineFigure->getAttribute('class'));
        if (in_array('latex-figure-inline', $classes)) {
          $extractedFigures[] = $inlineFigure;
        }
      }

      // Loop the extracted figures
      foreach ($extractedFigures as $figure) {
        /** @var DOMElement $figure */
        if (!$figure->hasAttribute('class')) {
          continue;
        }

        // Check for class
        $classes       = explode(' ', $figure->getAttribute('class'));
        $isInlineLatex = in_array('latex-figure-inline', $classes);
        $isLatex       = in_array('latex-figure', $classes);
        $isImage       = in_array('image', $classes);
        if (!$isInlineLatex && !$isLatex && !$isImage) {
          continue;
        }

        // Retrieve inner tags
        $img     = $figure->getElementsByTagName('img');
        $caption = $figure->getElementsByTagName('figcaption');

        // Check tag attributes
        if ($img->length < 1) {
          continue;
        }
        if (!$isInlineLatex) {
          if ($caption->length < 1) {
            continue;
          }
          if ($caption->item(0)->childNodes->length < 1) {
            continue;
          }
        }

        // Retrieve nodes
        /** @var DOMElement $imgElement */
        $imgElement = $img->item(0);
        if (!$isInlineLatex) {
          $captionElement = $caption->item(0)->childNodes->item(0);
        }

        // Retrieve information
        $id = md5($dom->saveHTML($imgElement));
        if (!$isInlineLatex && isset($captionElement)) {
          $caption = $dom->saveHTML($captionElement);
        }

        if ($isInlineLatex) {
          if (!$imgElement->hasAttribute('alt')) {
            continue;
          }

          // Retrieve relevant information
          $latex                  = $imgElement->getAttribute('alt');
          $inlineLatexImages[$id] = [
            'replace' => urldecode($latex),
            'caption' => '',
          ];
        } elseif ($isLatex) {
          if (!$imgElement->hasAttribute('alt')) {
            continue;
          }

          // Retrieve relevant information
          $latex            = $imgElement->getAttribute('alt');
          $latexImages[$id] = [
            'replace' => urldecode($latex),
            'caption' => $caption,
          ];
        } elseif ($isImage) {
          if (!$imgElement->hasAttribute('src')) {
            continue;
          }

          // Retrieve relevant information
          $image             = $imgElement->getAttribute('src');
          $normalImages[$id] = [
            'replace' => preg_replace('/(\/uploads\/studyarea\/)/ui', sprintf('%s$1', $this->projectDir), $image),
            'caption' => $caption,
          ];
        }

        // Place the placeholder
        $figure->parentNode->replaceChild($dom->createElement('span', sprintf('placeholder-%s', $id)), $figure);
      }

      // Remove any remaining, unprocessed images tags to prevent errors
      $remainingImages = [];
      foreach ($dom->getElementsByTagName('img') as $image) {
        $remainingImages[] = $image;
      }
      foreach ($remainingImages as $image) {
        /* @var DOMElement $image */
        $image->parentNode->removeChild($image);
      }

      if (count($extractedFigures) > 0 || count($remainingImages) > 0) {
        $html = $dom->saveHTML($dom);
      }
    }

    // Restore errors
    libxml_clear_errors();
    /* @phan-suppress-next-line PhanDeprecatedFunctionInternal */
    libxml_disable_entity_loader(false);

    $latex = $this->pandoc->convert($html, 'html', 'latex');

    // Replace latex image placeholders with action LaTeX code
    $latex = $this->replacePlaceholder($latex, $inlineLatexImages, '$%s%s$');
    $latex = $this->replacePlaceholder($latex, $latexImages, '\\begin{figure}[!htb]\\begin{displaymath}\boxed{%s}\\end{displaymath}\\caption*{%s}\\end{figure}');
    $latex = $this->replacePlaceholder($latex, $normalImages, '\\begin{figure}[!htb]\\includegraphics[frame]{%s}\\caption*{%s}\\end{figure}');

    // Replace unsupported graphics with an unavailable image
    $matches = [];
    preg_match_all('/\\\\includegraphics(\[.+\])?\{([^}]+)\}/u', (string)$latex, $matches);
    foreach ($matches[2] as $imageLocation) {
      if ($this->fileSystem->exists($imageLocation)) {
        $extension = strtolower(pathinfo((string)$imageLocation, PATHINFO_EXTENSION));
        if (in_array($extension, ['png', 'jpg', 'jpeg'])) {
          continue;
        }

        // Unsupported image found, replace with notice image
        $latex = str_replace($imageLocation, sprintf('%s%s/assets/img/print/notavailable.png', $this->projectDir, DIRECTORY_SEPARATOR), (string)$latex);
      }
    }

    // Replace local urls with full-path versions
    $latex = preg_replace('/\\\\href\{\/([^}]+)\}/ui', sprintf('\\\\href{%s$1}', $this->baseUrl), (string)$latex);

    return $latex;
  }

  private function replacePlaceholder(string $latex, array $replaceInfo, $replacement)
  {
    foreach ($replaceInfo as $id => $toReplace) {
      $new   = sprintf($replacement, $toReplace['replace'], $this->parser->parseText($toReplace['caption']));
      $latex = str_replace(sprintf('{placeholder-%s}', $id), $new, $latex);
    }

    return $latex;
  }
}
