<?php

namespace App\Controller;

use App\Request\Wrapper\RequestStudyArea;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UploadsController
 *
 * @author BobV
 *
 * @Route("/uploads")
 */
class UploadsController extends AbstractController
{
  /**
   * @Route("/studyarea/{_studyArea}/{path}", requirements={"_studyArea"="\d+", "path"=".+"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param Request          $request
   * @param RequestStudyArea $requestStudyArea
   * @param string           $path
   *
   * @return Response
   */
  public function load(Request $request, RequestStudyArea $requestStudyArea, string $path)
  {
    // Create path from request
    $fs            = new Filesystem();
    $requestedFile = sprintf('%s/public/uploads/studyarea/%s/%s',
        $this->getParameter("kernel.project_dir"),
        $requestStudyArea->getStudyArea()->getId(),
        $path);

    // Check if path exists
    if (!$fs->exists($requestedFile)) {
      throw $this->createNotFoundException();
    }

    // Create base response
    $download = $request->query->has('download');
    $response = $this->file($requestedFile, NULL, $download
        ? ResponseHeaderBag::DISPOSITION_ATTACHMENT
        : ResponseHeaderBag::DISPOSITION_INLINE);

    // Only cache when not downloading
    if (!$download) {
      // Disable symfony's automatic cache control header
      $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

      // Setup cache headers
      $response->setLastModified(\DateTime::createFromFormat('U', (string)filemtime($requestedFile)));
      $response->setAutoEtag();
      $response->setMaxAge(604800); // One week
      $response->setPrivate();

      // Check if response was cached: if so, the content is automatically purged
      $response->isNotModified($request);
    }

    return $response;
  }
}
