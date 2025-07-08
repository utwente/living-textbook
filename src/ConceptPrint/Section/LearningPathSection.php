<?php

namespace App\ConceptPrint\Section;

use App\Entity\Concept;
use App\Entity\LearningPath;
use App\Entity\LearningPathElement;
use App\Naming\NamingService;
use App\Router\LtbRouter;
use Bobv\LatexBundle\Exception\LatexException;
use Bobv\LatexBundle\Latex\Element\CustomCommand;
use Bobv\LatexBundle\Latex\Element\Listing;
use Bobv\LatexBundle\Latex\Element\Text;
use Bobv\LatexBundle\Latex\Section\SubSection;
use Pandoc\PandocException;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;

class LearningPathSection extends LtbSection
{
  /**
   * LearningPathSection constructor.
   *
   * @throws LatexException
   * @throws PandocException
   */
  public function __construct(
    LearningPath $learningPath, LtbRouter $router, TranslatorInterface $translator, NamingService $namingService, string $projectDir)
  {
    parent::__construct($learningPath->getName(), $router, $projectDir);

    $this->addElement(new Text(sprintf('\href{%s}{%s}',
      $this->router->generateBrowserUrl('app_learningpath_show', ['learningPath' => $learningPath->getId()]),
      $translator->trans('learning-path.online-source')
    )));

    // Add learning path data
    if ($learningPath->getIntroduction()) {
      $this->addElement(new CustomCommand('\\\\' . $this->convertHtmlToLatex($learningPath->getIntroduction())));
    }

    // Add question
    if ($learningPath->getQuestion()) {
      $this->addSection($translator->trans('learning-path.question'), $learningPath->getQuestion());
    }

    // Retrieve the concepts
    $concepts = $learningPath->getElementsOrdered()->map(fn (LearningPathElement $learningPathElement) => $learningPathElement->getConcept());

    // Add concept list
    $this->addElement(new SubSection($translator->trans('menu.concept'))
      ->addElement(new Listing($concepts->map(fn (Concept $concept) => $concept->getName())->toArray())));

    // Add each concept from the learning path
    foreach ($concepts as $concept) {
      $this->addElement(new CustomCommand('\\newpage'));
      $this->addElement(new ConceptSection($concept, $router, $translator, $namingService, $projectDir));
    }
  }
}
