<?php

namespace App\Controller;

use App\ConceptPrint\Base\ConceptPrint;
use App\ConceptPrint\Section\ConceptSection;
use App\ConceptPrint\Section\LearningPathSection;
use App\Entity\Concept;
use App\Entity\LearningPath;
use App\Entity\StudyArea;
use App\Naming\NamingService;
use App\Request\Wrapper\RequestStudyArea;
use App\Router\LtbRouter;
use BobV\LatexBundle\Exception\ImageNotFoundException;
use BobV\LatexBundle\Exception\LatexException;
use BobV\LatexBundle\Generator\LatexGeneratorInterface;
use BobV\LatexBundle\Helper\Sanitize;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
   * @IsGranted("STUDYAREA_PRINT", subject="requestStudyArea")
   *
   * @param RequestStudyArea        $requestStudyArea
   * @param Concept                 $concept
   * @param LatexGeneratorInterface $generator
   * @param TranslatorInterface     $translator
   * @param LtbRouter               $router
   * @param NamingService           $namingService
   *
   * @return Response
   * @throws Exception
   */
  public function printSingleConcept(
      RequestStudyArea $requestStudyArea, Concept $concept, LatexGeneratorInterface $generator,
      TranslatorInterface $translator, LtbRouter $router, NamingService $namingService)
  {
    // Check if correct study area
    if ($concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    $projectDir = $this->getParameter('kernel.project_dir');

    // Create LaTeX document
    $document = (new ConceptPrint($this->filename($concept->getName())))
        ->useLicenseImage($projectDir)
        ->setBaseUrl($this->generateUrl('base_url', [], UrlGeneratorInterface::ABSOLUTE_URL))
        ->setHeader($concept->getStudyArea(), $translator)
        ->addIntroduction($concept->getStudyArea(), $translator)
        ->addElement(new ConceptSection($concept, $router, $translator, $namingService, $projectDir));

    // Return PDF
    try {
      return $generator->createPdfResponse($document, false);
    } catch (Exception $e) {
      return $this->parsePrintException($e, $concept);
    }
  }

  /**
   * @Route("/learningpath/{learningPath}", requirements={"learningPath"="\d+"})
   *
   * @IsGranted("STUDYAREA_PRINT", subject="requestStudyArea")
   *
   * @param RequestStudyArea        $requestStudyArea
   * @param LearningPath            $learningPath
   * @param LatexGeneratorInterface $generator
   * @param TranslatorInterface     $translator
   * @param LtbRouter               $router
   * @param NamingService           $namingService
   *
   * @return Response
   * @throws Exception
   */
  public function printLearningPath(
      RequestStudyArea $requestStudyArea, LearningPath $learningPath, LatexGeneratorInterface $generator,
      TranslatorInterface $translator, LtbRouter $router, NamingService $namingService)
  {
    // Check if correct study area
    if ($learningPath->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    $projectDir = $this->getParameter('kernel.project_dir');

    // Create LaTeX document
    $document = (new ConceptPrint($this->filename($learningPath->getName())))
        ->useLicenseImage($projectDir)
        ->setBaseUrl($this->generateUrl('base_url', [], UrlGeneratorInterface::ABSOLUTE_URL))
        ->setHeader($learningPath->getStudyArea(), $translator)
        ->addIntroduction($learningPath->getStudyArea(), $translator)
        ->addElement(new LearningPathSection($learningPath, $router, $translator, $namingService, $projectDir));

    // Return PDF
    try {
      return $generator->createPdfResponse($document, false);
    } catch (Exception $e) {
      return $this->parsePrintException($e, NULL, $learningPath);
    }

  }

  private function filename(string $name)
  {
    return str_replace(' ', '-', mb_strtolower(Sanitize::sanitizeText($name)));
  }

  /**
   * Tries to parse the thrown exception into something that might be usable for the user.
   *
   * @param Exception         $e
   *
   * @param Concept|null      $concept
   * @param LearningPath|null $learningPath
   *
   * @return Response
   * @throws Exception
   */
  private function parsePrintException(Exception $e, Concept $concept = NULL, LearningPath $learningPath = NULL)
  {
    // Retrieve study area from one of the given objects
    $studyArea = $concept ? $concept->getStudyArea() : NULL;
    $studyArea = $studyArea ?? ($learningPath ? $learningPath->getStudyArea() : NULL);

    switch (true) {
      case $e instanceof ImageNotFoundException:
        assert($e instanceof ImageNotFoundException);
        $imageLocation = $e->getImageLocation();
        $isUrl         = $this->isUrl($imageLocation);

        return $this->render('print/image_not_found.html.twig', [
            'isUrl'        => $isUrl,
            'path'         => $isUrl ? $imageLocation : NULL,
            'basename'     => $isUrl ? NULL : $this->getProjectPath($imageLocation, $studyArea),
            'concept'      => $concept,
            'learningPath' => $learningPath,
            'studyArea'    => $studyArea,
        ]);
      case $e instanceof LatexException:
        assert($e instanceof LatexException);

        return $this->render('print/latex_error.html.twig', [
            'error' => $e,
        ]);
      default:
        throw $e;
    }
  }

  private function isUrl(string $url): bool
  {
    return 1 === preg_match('@^https?://@', $url);
  }

  private function getProjectPath(string $url, StudyArea $studyArea)
  {
    // Try to match against expected project path
    $matches = [];
    $regex   = sprintf('@^%s/public/uploads/studyarea/%d/(.+)@', $this->getParameter('kernel.project_dir'), $studyArea->getId());
    if (1 === preg_match($regex, $url, $matches)) {
      return $matches[1];
    }

    // Just return the base url
    return basename($url);
  }
}
