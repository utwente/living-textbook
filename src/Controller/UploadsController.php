<?php

namespace App\Controller;

use App\Request\Wrapper\RequestStudyArea;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UploadsController
 *
 * @author BobV
 *
 * @Route("/uploads")
 */
class UploadsController extends Controller
{
  /**
   * @Route("/studyarea/{_studyArea}/{path}", requirements={"_studyArea"="\d+", "path"=".+"})
   * @IsGranted("ROLE_USER")
   *
   * @param Request          $request
   * @param RequestStudyArea $studyArea
   * @param string           $path
   *
   * @return Response
   */
  public function load(Request $request, RequestStudyArea $studyArea, string $path)
  {
    // Create path from request
    $fs            = new Filesystem();
    $requestedFile = sprintf('%s/public/uploads/studyarea/%s/%s',
        $this->getParameter("kernel.project_dir"),
        $studyArea->getStudyArea()->getId(),
        $path);

    // Check if path exists
    if (!$fs->exists($requestedFile)) {
      throw $this->createNotFoundException();
    }

    // @todo Implement rights for image loading
    return $this->file($requestedFile, NULL, $request->query->has('download')
        ? ResponseHeaderBag::DISPOSITION_ATTACHMENT
        : ResponseHeaderBag::DISPOSITION_INLINE);
  }
}
