<?php

namespace App\ConceptPrint\Base;

use App\Entity\Concept;
use App\Entity\LearningPath;
use App\Entity\StudyArea;
use BobV\LatexBundle\Exception\LatexException;
use BobV\LatexBundle\Helper\Parser;
use BobV\LatexBundle\Latex\LatexBase;
use DateTime;
use Exception;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConceptPrint extends LatexBase
{

  /** @var Parser */
  private $parser;

  /** @var string */
  private $baseUrl;

  /**
   * Article constructor, sets defaults
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

        'headsep'  => '0.3in',
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
   * @throws LatexException
   */
  public function useLicenseImage(string $projectDir)
  {
    $this->setParam('licenseimage', sprintf('%s/assets/img/footer/license.png', $projectDir));

    return $this;
  }

  /**
   * Set the base url for the header
   *
   * @param string $baseUrl
   *
   * @return ConceptPrint
   */
  public function setBaseUrl(string $baseUrl)
  {
    $this->baseUrl = substr($baseUrl, strlen($baseUrl) - 1) == '/' ? substr($baseUrl, 0, strlen($baseUrl) - 1) : $baseUrl;

    return $this;
  }

  /**
   * @param Concept             $concept
   * @param TranslatorInterface $translator
   *
   * @return ConceptPrint
   * @throws LatexException
   */
  public function setTitleFromConcept(Concept $concept, TranslatorInterface $translator)
  {
    return $this->setTitle(
        $concept->getName(),
        $concept->getStudyArea(),
        $concept->getUpdatedAt(),
        $translator
    );
  }

  /**
   * @param LearningPath        $learningPath
   * @param TranslatorInterface $translator
   *
   * @return ConceptPrint
   * @throws LatexException
   */
  public function setTitleFromLearningPath(LearningPath $learningPath, TranslatorInterface $translator)
  {
    return $this->setTitle(
        $learningPath->getName(),
        $learningPath->getStudyArea(),
        $learningPath->getLastUpdated(),
        $translator
    );
  }

  /**
   * @param string              $title
   * @param StudyArea           $owner
   * @param DateTime            $lastUpdate
   * @param TranslatorInterface $translator
   *
   * @return $this
   * @throws LatexException
   */
  private function setTitle(string $title, StudyArea $owner, DateTime $lastUpdate, TranslatorInterface $translator)
  {

    if ($this->baseUrl == NULL) {
      throw new InvalidArgumentException('Missing base url, make sure to set it before calling this method!');
    }

    $baseHeader = $translator->trans('print.header', [
        '%title%' => $this->parser->parseText($title),
        '%owner%' => $this->parser->parseText($owner->getOwner()->getFamilyName()),
        '%year%'  => $lastUpdate->format('Y'),
        '%url%'   => $this->parser->parseText($this->baseUrl),
    ]);

    $this->setParam('head', $baseHeader);

    return $this;
  }
}
