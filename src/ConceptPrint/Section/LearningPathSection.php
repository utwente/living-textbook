<?php

namespace App\ConceptPrint\Section;

use App\Entity\Concept;
use App\Entity\LearningPath;
use App\Entity\LearningPathElement;
use BobV\LatexBundle\Exception\LatexException;
use BobV\LatexBundle\Latex\Element\CustomCommand;
use BobV\LatexBundle\Latex\Element\Listing;
use BobV\LatexBundle\Latex\Element\Text;
use BobV\LatexBundle\Latex\Section\SubSection;
use Pandoc\PandocException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LearningPathSection extends LtbSection
{

  /**
   * LearningPathSection constructor.
   *
   * @param LearningPath        $learningPath
   * @param RouterInterface     $router
   * @param TranslatorInterface $translator
   * @param string              $projectDir
   *
   * @throws LatexException
   * @throws PandocException
   */
  public function __construct(LearningPath $learningPath, RouterInterface $router, TranslatorInterface $translator, string $projectDir)
  {
    parent::__construct($learningPath->getName(), $router, $translator, $projectDir);

    $pathWithoutMap = $this->router->generate('app_learningpath_show', ['learningPath' => $learningPath->getId()], RouterInterface::ABSOLUTE_PATH);
    $this->addElement(new Text(sprintf('\href{%s}{%s}',
        $this->router->generate('_home_simple', ['pageUrl' => ltrim($pathWithoutMap, '/')], RouterInterface::ABSOLUTE_URL),
        $this->translator->trans('learning-path.online-source')
    )));

    // Add learning path data
    if ($learningPath->getIntroduction() != '') {
      $this->addElement(new CustomCommand('\\\\' . $this->convertHtmlToLatex($learningPath->getIntroduction())));
    }

    // Add question
    if ($learningPath->getQuestion() != '') {
      $this->addSection('learning-path.question', $learningPath->getQuestion());
    }

    // Retrieve the concepts
    $concepts = $learningPath->getElementsOrdered()->map(function (LearningPathElement $learningPathElement) {
      return $learningPathElement->getConcept();
    });

    // Add concept list
    $this->addElement((new SubSection($this->translator->trans('menu.concept')))
        ->addElement(new Listing($concepts->map(function (Concept $concept) {
          return $concept->getName();
        })->toArray())));

    // Add each concept from the learning path
    foreach ($concepts as $concept) {
      $this->addElement(new CustomCommand('\\newpage'));
      $this->addElement(new ConceptSection($concept, $router, $translator, $projectDir));
    }
  }
}
