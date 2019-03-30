<?php

namespace App\Controller;

use App\Entity\PageLoad;
use App\Entity\User;
use App\Excel\TrackingExportBuilder;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class TrackingController
 *
 * @Route("/{_studyArea}/track", requirements={"_studyArea"="\d+"})
 */
class TrackingController extends AbstractController
{

  /**
   * @Route("/export")
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param RequestStudyArea      $requestStudyArea
   * @param TrackingExportBuilder $builder
   *
   * @return Response
   * @throws Exception
   */
  public function export(RequestStudyArea $requestStudyArea, TrackingExportBuilder $builder)
  {
    return $builder->build($requestStudyArea->getStudyArea());
  }

  /**
   * @Route("/pageload", methods={"POST"}, options={"expose"="true"})
   * @IsGranted("ROLE_USER")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param EntityManagerInterface $em
   * @param SerializerInterface    $serializer
   * @param ValidatorInterface     $validator
   * @param RouterInterface        $router
   *
   * @return Response
   */
  public function pageload(Request $request, RequestStudyArea $requestStudyArea, EntityManagerInterface $em,
                           SerializerInterface $serializer, ValidatorInterface $validator, RouterInterface $router)
  {
    $pageLoad = $serializer->deserialize($request->getContent(), PageLoad::class, 'json');
    assert($pageLoad instanceof PageLoad);
    /** @var User $user */
    $user = $this->getUser();

    // Add more context to object
    $pageLoad
        ->setStudyArea($requestStudyArea->getStudyArea())
        ->setUserId($user->getUsername())
        ->setPathContext($router->match($pageLoad->getPath()))
        ->setOriginContext($pageLoad->getOrigin() ? $router->match($pageLoad->getOrigin()) : NULL);

    // Validate object
    $violations = $validator->validate($pageLoad);
    if (count($violations) != 0) {
      throw new BadRequestHttpException();
    }

    // Save data
    $em->persist($pageLoad);
    $em->flush();

    // Return OK response
    return new Response();
  }
}
