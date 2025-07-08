<?php

namespace App\Controller;

use App\Repository\StudyAreaRepository;
use Drenso\Shared\Exception\NullGuard\IdRequiredException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function preg_match;
use function sprintf;

/** Study area id 0 is used to indicate the global storage space. */
#[Route('/elfinder')]
class ElFinderController extends AbstractController
{
  #[Route('/load/{instance}', name: 'ef_connect', options: ['no_login_wrap' => true], defaults: ['instance' => 'default'])]
  #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
  public function load(Request $request, string $instance, StudyAreaRepository $studyAreaRepository): Response
  {
    // Parse study area from the home folder
    if (null === ($homeFolderString = $request->query->get('homeFolder', null))) {
      // Home folder query parameter not found
      throw $this->createNotFoundException();
    }

    // Match for study area id
    if (1 === preg_match("/^studyarea\/(\d+)/", $homeFolderString, $result)) {
      $studyAreaId = (int)$result[1];

      if (!($studyArea = $studyAreaRepository->find($studyAreaId))) {
        throw $this->createNotFoundException();
      }

      $this->denyAccessUnlessGranted('STUDYAREA_EDIT', $studyArea);
    } elseif (1 === preg_match('/^global/', $homeFolderString, $result)) {
      $studyAreaId = 0;
      $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
    } else {
      throw $this->createNotFoundException();
    }

    return $this->forwardToElFinder('load', $instance, $studyAreaId, $request->query->all());
  }

  #[Route('/show/{instance}/{studyAreaId<\d+>}', name: 'elfinder', options: ['no_login_wrap' => true], defaults: ['instance' => 'default'])]
  #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
  public function show(Request $request, string $instance, int $studyAreaId, StudyAreaRepository $studyAreaRepository): Response
  {
    $studyArea = null;

    if ($studyAreaId !== 0) {
      // Check for edit permissions of the referenced study area
      if (!($studyArea = $studyAreaRepository->find($studyAreaId))) {
        throw $this->createNotFoundException();
      }

      $this->denyAccessUnlessGranted('STUDYAREA_EDIT', $studyArea);
    } else {
      // If no area, check for super admin
      $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
    }

    return $this->forwardToElFinder('show', $instance, $studyArea ? ($studyArea->getId() ?? throw new IdRequiredException()) : 0, $request->query->all());
  }

  /** Forward the request to the correct elfinder controller. */
  protected function forwardToElFinder(string $action, string $instance, int $studyAreaId, array $query): Response
  {
    // Check whether the folder for the study area exists
    $folder     = $studyAreaId === 0
        ? 'global'
        : sprintf('studyarea/%d', $studyAreaId);
    $folderPath = sprintf('%s/uploads/%s', $this->getParameter('kernel.project_dir'), $folder);
    $filesystem = new Filesystem();
    if (!$filesystem->exists($folderPath)) {
      $filesystem->mkdir($folderPath);
    }

    // Forward to the original ELfinder controller
    return $this->forward(sprintf('FM\ElfinderBundle\Controller\ElFinderController::%s', $action), [
      'instance'   => $instance,
      'homeFolder' => $folder,
    ], $query);
  }
}
