<?php

namespace App\Controller;

use App\ConceptPrint\Base\ConceptPrint;
use App\Entity\Concept;
use App\Request\Wrapper\RequestStudyArea;
use BobV\LatexBundle\Generator\LatexGeneratorInterface;
use BobV\LatexBundle\Helper\Sanitize;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
   *
   * @return Response
   * @throws \Exception
   */
  public function printSingleConcept(RequestStudyArea $requestStudyArea, Concept $concept, LatexGeneratorInterface $generator, KernelInterface $kernel)
  {
    // Check if correct study area
    if ($concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    // Create LaTeX document
    $document = (new ConceptPrint($this->filename($concept->getName())))
        ->useLicenseImage($kernel->getProjectDir())
        ->setConcept($concept, $this->generateUrl('app_default_landing', [], UrlGeneratorInterface::ABSOLUTE_URL));

    // Return PDF
    return $generator->createPdfResponse($document, false);
  }

  private function filename(string $name)
  {
    return str_replace(' ', '-', mb_strtolower(Sanitize::sanitizeText($name)));
  }
}
