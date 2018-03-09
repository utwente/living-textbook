<?php

namespace App\Controller;

use App\Entity\StudyArea;
use App\Repository\StudyAreaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
class ElFinderController extends Controller
{

  /**
   * @Route("/load/{instance}/{studyArea}", defaults={"instance"="default","studyArea"="null"}, name="ef_connect")
   *
   * @param Request                $request
   * @param string                 $instance
   * @param StudyArea|null         $studyArea
   * @param EntityManagerInterface $em
   *
   * @return Response
   */
  public function load(Request $request, string $instance, ?StudyArea $studyArea, EntityManagerInterface $em)
  {
    // Check study area
    if ($studyArea == NULL) {
      $studyAreaRepo = $em->getRepository('App:StudyArea');
      assert($studyAreaRepo instanceof StudyAreaRepository);
      $studyArea = $studyAreaRepo->findDefault();
    }

    $this->checkPermissions($studyArea);

    return $this->forwardToElFinder('load', $instance, $studyArea, $request->query->all());
  }

  /**
   * @Route("/show/{instance}/{studyArea}", defaults={"instance"="default","studyArea"="null"}, name="elfinder")
   *
   * @param Request                $request
   * @param string                 $instance
   * @param StudyArea|null         $studyArea
   * @param EntityManagerInterface $em
   *
   * @return Response
   */
  public function show(Request $request, string $instance, ?StudyArea $studyArea, EntityManagerInterface $em)
  {
    if ($studyArea == NULL) {
      $studyAreaRepo = $em->getRepository('App:StudyArea');
      assert($studyAreaRepo instanceof StudyAreaRepository);
      $studyArea = $studyAreaRepo->findDefault();
    }

    $this->checkPermissions($studyArea);

    return $this->forwardToElFinder('show', $instance, $studyArea, $request->query->all());
  }

  /**
   * Check the permissions for the requested study area
   *
   * @param StudyArea $studyArea
   */
  private function checkPermissions(StudyArea $studyArea)
  {
    // @todo check permissions for the study area
    // throw new NotFoundException();
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
    $folder = sprintf('studyarea/%d', $studyArea->getId());
    $folderPath = sprintf('%s/public/uploads/%s', $this->getParameter("kernel.project_dir"), $folder);
    $filesystem = new Filesystem();
    if (!$filesystem->exists($folderPath)){
      $filesystem->mkdir($folderPath);
    }

    // Forward to the original ELfinder controller
    return $this->forward(sprintf('FM\ElfinderBundle\Controller\ElFinderController::%sAction', $action), [
        'instance'   => $instance,
        'homeFolder' => $folder,
    ], $query);
  }
}
