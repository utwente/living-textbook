<?php

namespace App\ConceptPrint\Base;

use App\Entity\StudyArea;
use Bobv\LatexBundle\Exception\LatexException;
use Bobv\LatexBundle\Helper\Parser;
use Bobv\LatexBundle\Latex\Element\CustomCommand;
use Bobv\LatexBundle\Latex\Element\Text;
use Bobv\LatexBundle\Latex\LatexBase;
use Bobv\LatexBundle\Latex\Section\SubSection;
use DateTime;
use Exception;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConceptPrint extends LatexBase
{
  private Parser $parser;

  private ?string $baseUrl = null;

  /**
   * Article constructor, sets defaults.
   *
   * @param string $filename
   *
   * @throws Exception
   */
  public function __construct($filename)
  {
    // Define standard values
    $this->parser   = new Parser();
    $dateTime       = new DateTime();
    $this->template = 'concept_print/base/concept_print.tex.twig';

    $this->params = [
        'options' => null,

        'licenseimage' => false,

        'head' => '', // Header

        'lfoot' => $dateTime->format('Y-m-d G:i'), // Bottom left footer
        'rfoot' => 'Page\ \thepage\ of\ \pageref{LastPage}', // Bottom right footer

        'topmargin'    => '0.5in', // Some document margins
        'leftmargin'   => '2.4in', // Some document margins
        'rightmargin'  => '0.8in', // Some document margins
        'bottommargin' => '0.5in', // Some document margins

        'headsep'  => '0.3in',
        'footskip' => '0.2in',

        'linespread' => '1.1', // Line spacing

        'headrulewidth' => '0.4pt', // Header size
        'footrulewidth' => '0.4pt', // Footer size

        'parindent' => '0pt', // Remove parindentation

        'extra_commands' => [], // Define extra commands if needed
        'packages'       => [], // Define extra packages to use
    ];

    // Call parent constructor
    parent::__construct($filename);
  }

  /**
   * @throws LatexException
   *
   * @return ConceptPrint
   */
  public function useLicenseImage(string $projectDir)
  {
    $this->setParam('licenseimage', sprintf('%s/assets/img/footer/license.png', $projectDir));

    return $this;
  }

  /**
   * Set the base url for the header.
   *
   * @return ConceptPrint
   */
  public function setBaseUrl(string $baseUrl)
  {
    $this->baseUrl = substr($baseUrl, strlen($baseUrl) - 1) == '/' ? substr($baseUrl, 0, strlen($baseUrl) - 1) : $baseUrl;

    return $this;
  }

  /**
   * Set the header for a print.
   *
   * @throws LatexException
   *
   * @return ConceptPrint
   */
  public function setHeader(StudyArea $studyArea, TranslatorInterface $translator)
  {
    if ($this->baseUrl == null) {
      throw new InvalidArgumentException('Missing base url, make sure to set it before calling this method!');
    }

    $baseHeader = $translator->trans('print.header', [
        '%header%' => $studyArea->getPrintHeader() ? $this->parser->parseText($studyArea->getPrintHeader()) : '',
        '%url%'    => $this->parser->parseText($this->baseUrl),
    ]);

    $this->setParam('head', $baseHeader);

    return $this;
  }

  /**
   * Add the introduction text for a print.
   *
   * @throws LatexException
   *
   * @return ConceptPrint
   */
  public function addIntroduction(StudyArea $studyArea, TranslatorInterface $translator)
  {
    // Only add the introduction when one is defined
    if ($studyArea->getPrintIntroduction()) {
      $this->addElement((new SubSection($translator->trans('study-area.print-introduction-header')))
          ->addElement(new Text($studyArea->getPrintIntroduction())));
      $this->addElement(new CustomCommand('\\newpage'));
    }

    return $this;
  }
}
