<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
   * @Route("/{path}", requirements={"path"=".+"})
   * @param Request $request
   * @param         $path
   *
   * @return Response
   */
  public function load(Request $request, string $path)
  {
    // Create path from request
    $fs            = new Filesystem();
    $requestedFile = sprintf('%s/public/uploads/%s', $this->getParameter("kernel.project_dir"), $path);

    // Check if path exists
    if (!$fs->exists($requestedFile)) {
      throw $this->createNotFoundException();
    }

    // @todo Implement right for image loading
    return $this->file($requestedFile, NULL, $request->query->has('download')
        ? ResponseHeaderBag::DISPOSITION_ATTACHMENT
        : ResponseHeaderBag::DISPOSITION_INLINE);
  }
}
