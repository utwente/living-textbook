<?php

namespace App\Controller;

use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/uploads')]
class UploadsController extends AbstractController
{
  #[Route('/studyarea/{_studyArea<\d+>}/{path<.+>}', options: ['no_login_wrap' => true])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function load(Request $request, RequestStudyArea $requestStudyArea, string $path): Response
  {
    // Manual check to not trigger login forward
    if (!$this->isGranted(StudyAreaVoter::SHOW, $requestStudyArea)) {
      return new Response(status: Response::HTTP_FORBIDDEN);
    }

    // Create path from request
    $requestedFile = sprintf('%s/uploads/studyarea/%s/%s',
      $this->getParameter('kernel.project_dir'),
      $requestStudyArea->getStudyArea()->getId(),
      $path);

    return $this->getFile($request, $requestedFile);
  }

  #[Route('/global/{path}', options: ['no_login_wrap' => true])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function loadGlobal(Request $request, string $path): Response
  {
    // Create path from request
    $requestedFile = sprintf('%s/uploads/global/%s',
      $this->getParameter('kernel.project_dir'),
      $path);

    return $this->getFile($request, $requestedFile);
  }

  private function getFile(Request $request, string $requestedFile): BinaryFileResponse
  {
    // Check if path exists
    $fs = new Filesystem();
    if (!$fs->exists($requestedFile) || !is_file($requestedFile)) {
      throw $this->createNotFoundException();
    }

    // Create base response
    $download = $request->query->has('download');
    $response = $this->file($requestedFile, null, $download
      ? ResponseHeaderBag::DISPOSITION_ATTACHMENT
      : ResponseHeaderBag::DISPOSITION_INLINE);

    // Only cache when not downloading
    if (!$download) {
      // Disable symfony's automatic cache control header
      $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

      // Setup cache headers
      $response->setLastModified(DateTime::createFromFormat('U', (string)filemtime($requestedFile)));
      $response->setAutoEtag();
      $response->setMaxAge(604800); // One week
      $response->setPrivate();

      // Check if response was cached: if so, the content is automatically purged
      $response->isNotModified($request);
    }

    return $response;
  }
}
