<?php

namespace App\Controller;

use App\Entity\PageLoad;
use App\Entity\StudyArea;
use App\Entity\TrackingEvent;
use App\Entity\User;
use App\Excel\TrackingExportBuilder;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function count;
use function trim;

#[Route('/{_studyArea<\d+>}/track')]
class TrackingController extends AbstractController
{
  /** @throws Exception */
  #[Route('/export')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  public function export(RequestStudyArea $requestStudyArea, TrackingExportBuilder $builder): Response
  {
    // Verify whether tracking is actually enabled
    $studyArea = $requestStudyArea->getStudyArea();
    if (!$studyArea->isTrackUsers()) {
      return $this->render('tracking/not_enabled.html.twig', ['studyArea' => $studyArea]);
    }

    return $builder->buildResponse($studyArea);
  }

  #[Route('/pageload', options: ['expose' => 'true'], methods: [Request::METHOD_POST])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function pageload(
    Request $request, RequestStudyArea $requestStudyArea, EntityManagerInterface $em, SerializerInterface $serializer,
    ValidatorInterface $validator, RouterInterface $router): Response
  {
    return $this->processTrackingItem(
      PageLoad::class,
      static function (PageLoad $pageLoad, StudyArea $studyArea, ?User $user) use ($router) {
        $pathContext = null;
        try {
          $pathContext = $router->match($pageLoad->getPath());
        } catch (ResourceNotFoundException) {
          // Try to resolve the path context, set empty context when route not found
        }

        $originContext = null;
        if ($pageLoad->getOrigin()) {
          try {
            $originContext = $router->match($pageLoad->getOrigin());
          } catch (ResourceNotFoundException) {
            // Try to resolve the origin context, but ignore when not matched
          }
        }

        // Set the updated data
        $pageLoad
          ->setStudyArea($studyArea)
          ->setUserId($user ? $user->getUserIdentifier() : 'anonymous')
          ->setPathContext($pathContext)
          ->setOriginContext($originContext);
      },
      $request, $requestStudyArea, $em, $serializer, $validator);
  }

  #[Route('/event', options: ['expose' => 'true'], methods: [Request::METHOD_POST])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function event(
    Request $request, RequestStudyArea $requestStudyArea, EntityManagerInterface $em, SerializerInterface $serializer,
    ValidatorInterface $validator): Response
  {
    return $this->processTrackingItem(
      TrackingEvent::class,
      static function (TrackingEvent $pageLoad, StudyArea $studyArea, ?User $user) {
        $pageLoad
          ->setStudyArea($studyArea)
          ->setUserId($user ? $user->getUserIdentifier() : 'anonymous');
      },
      $request, $requestStudyArea, $em, $serializer, $validator);
  }

  /** Process a tracking item request. */
  private function processTrackingItem(
    string $clazz, Closure $callback, Request $request, RequestStudyArea $requestStudyArea,
    EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): Response
  {
    // Verify whether tracking is actually enabled
    $studyArea = $requestStudyArea->getStudyArea();
    if (!$studyArea->isTrackUsers()) {
      throw new BadRequestHttpException();
    }

    $objects = $serializer->deserialize($request->getContent(), "array<$clazz>", 'json');

    // Add more context to object
    foreach ($objects as $object) {
      $callback($object, $studyArea, $this->getUser());
    }

    // Validate object
    $violations = $validator->validate($objects);
    if (count($violations) != 0) {
      $returnErrorString = '';
      foreach ($violations as $error) {
        $returnErrorString .= ' ' . (string)$error;
      }

      return new Response(trim($returnErrorString), 400);
    }

    // Save data
    foreach ($objects as $object) {
      $em->persist($object);
    }
    $em->flush();

    // Return OK response
    return new Response();
  }
}
