<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserBrowserState;
use App\Repository\UserBrowserStateRepository;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_studyArea}/browser/state", requirements={"_studyArea"="\d+"})
 */
class BrowserStateController extends AbstractController
{
  /**
   * Retrieve the current filter state
   *
   * @Route("/filter", methods={"GET"}, options={"expose"=true})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea           $requestStudyArea
   * @param UserBrowserStateRepository $repository
   * @param SerializerInterface        $serializer
   *
   * @return Response
   */
  public function filterState(
      RequestStudyArea $requestStudyArea, UserBrowserStateRepository $repository,
      SerializerInterface $serializer): Response
  {
    if (!$user = $this->getUser()) {
      return new Response(NULL, Response::HTTP_FORBIDDEN);
    }
    assert($user instanceof User);

    if (!$state = $repository->findForUser($user, $requestStudyArea->getStudyArea())) {
      throw $this->createNotFoundException();
    }

    return JsonResponse::fromJsonString($serializer->serialize($state->getFilterState(), 'json'));
  }

  /**
   * Stores the current filter state
   *
   * @Route("/filter", methods={"POST"}, options={"expose"=true})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param Request                    $request
   * @param RequestStudyArea           $requestStudyArea
   * @param UserBrowserStateRepository $repository
   * @param SerializerInterface        $serializer
   * @param EntityManagerInterface     $em
   *
   * @return Response
   */
  public function storeFilterState(
      Request $request, RequestStudyArea $requestStudyArea, UserBrowserStateRepository $repository,
      SerializerInterface $serializer, EntityManagerInterface $em): Response
  {
    if (!$user = $this->getUser()) {
      return new Response(NULL, Response::HTTP_FORBIDDEN);
    }
    assert($user instanceof User);

    if (!$state = $repository->findForUser($user, $requestStudyArea->getStudyArea())) {
      $state = (new UserBrowserState())
          ->setUser($user)
          ->setStudyArea($requestStudyArea->getStudyArea());
    }

    $state->setFilterState($serializer->deserialize($request->getContent(), 'array', 'json'));
    $em->persist($state);
    $em->flush();

    return JsonResponse::fromJsonString($serializer->serialize($state->getFilterState(), 'json'));
  }
}
