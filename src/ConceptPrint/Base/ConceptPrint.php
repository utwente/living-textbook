<?php

namespace App\ConceptPrint\Base;

use App\Entity\Concept;
use BobV\LatexBundle\Helper\Parser;
use BobV\LatexBundle\Latex\LatexBase;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConceptPrint extends LatexBase
{

  private $parser;

  /**
   * Article constructor, sets defaults
   *
   * @param string $filename
   *
   * @throws \Exception
   */
  public function __construct($filename)
  {
    // Define standard values
    $this->parser   = new Parser();
    $dateTime       = new \DateTime();
    $this->template = 'concept_print/base/concept_print.tex.twig';

    $this->params = array(
        'options' => NULL,

        'licenseimage' => false,

        'head' => '', // Header

        'lfoot' => $dateTime->format('Y-m-d G:i'), // Bottom left footer
        'rfoot' => 'Page\ \thepage\ of\ \pageref{LastPage}', // Bottom right footer

        'topmargin'    => '0.5in', // Some document margins
        'leftmargin'   => '2.4in', // Some document margins
        'rightmargin'  => '0.8in', // Some document margins
        'bottommargin' => '0.5in', // Some document margins

        'headsep'  => '0.1in',
        'footskip' => '0.2in',

        'linespread' => '1.1', // Line spacing

        'headrulewidth' => '0.4pt', // Header size
        'footrulewidth' => '0.4pt', // Footer size

        'parindent' => '0pt', // Remove parindentation

        'extra_commands' => array(), // Define extra commands if needed
        'packages'       => array(), // Define extra packages to use
    );

    // Call parent constructor
    parent::__construct($filename);
  }

  /**
   * @param string $projectDir
   *
   * @return ConceptPrint
   * @throws \BobV\LatexBundle\Exception\LatexException
   */
  public function useLicenseImage(string $projectDir)
  {
    $this->setParam('licenseimage', sprintf('%s/assets/img/footer/license.png', $projectDir));

    return $this;
  }

  /**
   * @param Concept             $concept
   * @param string              $baseUrl
   * @param TranslatorInterface $translator
   *
   * @return ConceptPrint
   * @throws \BobV\LatexBundle\Exception\LatexException
   */
  public function setConcept(Concept $concept, string $baseUrl, TranslatorInterface $translator)
  {
    $baseUrl    = substr($baseUrl, strlen($baseUrl) - 1) == '/' ? substr($baseUrl, 0, strlen($baseUrl) - 1) : $baseUrl;
    $baseHeader = $translator->trans('print.header', [
        '%owner%'   => $this->parser->parseText($concept->getStudyArea()->getOwner()->getFamilyName()),
        '%year%'    => $concept->getUpdatedAt()->format('Y'),
        '%concept%' => $this->parser->parseText($concept->getName()),
        '%url%'     => $baseUrl,
    ]);

    $this->setParam('head', $baseHeader);

    return $this;
  }
}
