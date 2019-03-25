<?php

namespace App\Controller;

use App\ConceptPrint\Base\ConceptPrint;
use App\ConceptPrint\Section\ConceptSection;
use App\Entity\Concept;
use App\Request\Wrapper\RequestStudyArea;
use BobV\LatexBundle\Generator\LatexGeneratorInterface;
use BobV\LatexBundle\Helper\Sanitize;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PrintController
 *
 * @Route("/{_studyArea}/print", requirements={"_studyArea"="\d+"})
 */
class PrintController extends AbstractController
{

  /**
   * @Route("/concept/{concept}", requirements={"concept"="\d+"})
   *
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea        $requestStudyArea
   * @param Concept                 $concept
   * @param LatexGeneratorInterface $generator
   * @param TranslatorInterface     $translator
   * @param RouterInterface         $router
   *
   * @return Response
   * @throws \Exception
   */
  public function printSingleConcept(RequestStudyArea $requestStudyArea, Concept $concept, LatexGeneratorInterface $generator,
                                     TranslatorInterface $translator, RouterInterface $router)
  {
    // Check if correct study area
    if ($concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    $projectDir = $this->getParameter('kernel.project_dir');

    // Create LaTeX document
    $document = (new ConceptPrint($this->filename($concept->getName())))
        ->useLicenseImage($projectDir)
        ->setConcept($concept,
            $this->generateUrl('base_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $translator)
        ->addElement(new ConceptSection($concept, $router, $translator, $projectDir));

    // Return PDF
    return $generator->createPdfResponse($document, false);
  }

  private function filename(string $name)
  {
    return str_replace(' ', '-', mb_strtolower(Sanitize::sanitizeText($name)));
  }
}
