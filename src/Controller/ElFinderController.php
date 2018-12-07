<?php

namespace App\Controller;

use App\Entity\StudyArea;
use App\Repository\StudyAreaRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ElFinderController
 *
 * @author BobV
 *
 * @Route("/elfinder")
 */
class ElFinderController extends AbstractController
{

  /**
   * @Route("/load/{instance}", defaults={"instance"="default"}, name="ef_connect")
   * @IsGranted("ROLE_USER")
   *
   * @param Request             $request
   * @param string              $instance
   * @param StudyAreaRepository $studyAreaRepository
   *
   * @return Response
   */
  public function load(Request $request, string $instance, StudyAreaRepository $studyAreaRepository)
  {
    // Parse study area from the home folder
    if (NULL === ($homeFolderString = $request->query->get('homeFolder', NULL))) {
      // Home folder query parameter not found
      throw $this->createNotFoundException();
    }

    // Match for study area id
    preg_match("/^studyarea\/(\d+)/", $homeFolderString, $result);
    $studyAreaId = intval($result[1]);
    if (!($studyArea = $studyAreaRepository->find($studyAreaId))) {
      throw $this->createNotFoundException();
    }
    assert($studyArea instanceof StudyArea);

    $this->denyAccessUnlessGranted('STUDYAREA_EDIT', $studyArea);

    return $this->forwardToElFinder('load', $instance, $studyArea, $request->query->all());
  }

  /**
   * @Route("/show/{instance}/{studyArea}", defaults={"instance"="default"}, name="elfinder")
   * @IsGranted("ROLE_USER")
   *
   * @param Request   $request
   * @param string    $instance
   * @param StudyArea $studyArea
   *
   * @return Response
   */
  public function show(Request $request, string $instance, StudyArea $studyArea)
  {
    // Check for edit permissions
    $this->denyAccessUnlessGranted('STUDYAREA_EDIT', $studyArea);

    return $this->forwardToElFinder('show', $instance, $studyArea, $request->query->all());
  }

  /**
   * Forward the request to the correct elfinder controller
   *
   * @param string    $action
   * @param string    $instance
   * @param StudyArea $studyArea
   * @param array     $query
   *
   * @return Response
   */
  protected function forwardToElFinder(string $action, string $instance, StudyArea $studyArea, array $query)
  {
    // Check whether the folder for the study area exists
    $folder     = sprintf('studyarea/%d', $studyArea->getId());
    $folderPath = sprintf('%s/public/uploads/%s', $this->getParameter("kernel.project_dir"), $folder);
    $filesystem = new Filesystem();
    if (!$filesystem->exists($folderPath)) {
      $filesystem->mkdir($folderPath);
    }

    // Forward to the original ELfinder controller
    return $this->forward(sprintf('FM\ElfinderBundle\Controller\ElFinderController::%sAction', $action), [
        'instance'   => $instance,
        'homeFolder' => $folder,
    ], $query);
  }
}
