<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserBrowserState;
use App\Repository\UserBrowserStateRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function assert;

#[Route('/{_studyArea<\d+>}/browser/state')]
class BrowserStateController extends AbstractController
{
  /** Retrieve the current filter state. */
  #[Route('/filter', options: ['expose' => true], methods: ['GET'])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function filterState(
    RequestStudyArea $requestStudyArea, UserBrowserStateRepository $repository,
    SerializerInterface $serializer): Response
  {
    if (!$user = $this->getUser()) {
      return new Response(null, Response::HTTP_FORBIDDEN);
    }
    assert($user instanceof User);

    if (!$state = $repository->findForUser($user, $requestStudyArea->getStudyArea())) {
      throw $this->createNotFoundException();
    }

    return JsonResponse::fromJsonString($serializer->serialize($state->getFilterState(), 'json'));
  }

  /** Stores the current filter state. */
  #[Route('/filter', options: ['expose' => true], methods: ['POST'])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function storeFilterState(
    Request $request, RequestStudyArea $requestStudyArea, UserBrowserStateRepository $repository,
    SerializerInterface $serializer, EntityManagerInterface $em): Response
  {
    if (!$user = $this->getUser()) {
      return new Response(null, Response::HTTP_FORBIDDEN);
    }
    assert($user instanceof User);

    if (!$state = $repository->findForUser($user, $requestStudyArea->getStudyArea())) {
      $state = new UserBrowserState()
        ->setUser($user)
        ->setStudyArea($requestStudyArea->getStudyArea());
    }

    $state->setFilterState($serializer->deserialize($request->getContent(), 'array', 'json'));
    $em->persist($state);
    $em->flush();

    return JsonResponse::fromJsonString($serializer->serialize($state->getFilterState(), 'json'));
  }
}
