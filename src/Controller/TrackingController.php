<?php

namespace App\Controller;

use App\Entity\PageLoad;
use App\Entity\StudyArea;
use App\Entity\TrackingEvent;
use App\Entity\User;
use App\Excel\TrackingExportBuilder;
use App\Request\Wrapper\RequestStudyArea;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class TrackingController.
 *
 * @Route("/{_studyArea}/track", requirements={"_studyArea"="\d+"})
 */
class TrackingController extends AbstractController
{
  /**
   * @Route("/export")
   *
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @throws Exception
   *
   * @return Response
   */
  public function export(RequestStudyArea $requestStudyArea, TrackingExportBuilder $builder)
  {
    // Verify whether tracking is actually enabled
    $studyArea = $requestStudyArea->getStudyArea();
    if (!$studyArea->isTrackUsers()) {
      return $this->render('tracking/not_enabled.html.twig', ['studyArea' => $studyArea]);
    }

    return $builder->buildResponse($studyArea);
  }

  /**
   * @Route("/pageload", methods={"POST"}, options={"expose"="true"})
   *
   * @IsGranted("PUBLIC_ACCESS")
   *
   * @return Response
   */
  public function pageload(
    Request $request, RequestStudyArea $requestStudyArea, EntityManagerInterface $em, SerializerInterface $serializer,
    ValidatorInterface $validator, RouterInterface $router)
  {
    return $this->processTrackingItem(
      PageLoad::class,
      function (PageLoad $pageLoad, StudyArea $studyArea, ?User $user) use ($router) {
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

  /**
   * @Route("/event", methods={"POST"}, options={"expose"="true"})
   *
   * @IsGranted("PUBLIC_ACCESS")
   *
   * @return Response
   */
  public function event(
    Request $request, RequestStudyArea $requestStudyArea, EntityManagerInterface $em, SerializerInterface $serializer,
    ValidatorInterface $validator)
  {
    return $this->processTrackingItem(
      TrackingEvent::class,
      function (TrackingEvent $pageLoad, StudyArea $studyArea, ?User $user) {
        $pageLoad
          ->setStudyArea($studyArea)
          ->setUserId($user ? $user->getUserIdentifier() : 'anonymous');
      },
      $request, $requestStudyArea, $em, $serializer, $validator);
  }

  /**
   * Process a tracking item request.
   *
   * @return Response
   */
  private function processTrackingItem(
    string $clazz, Closure $callback, Request $request, RequestStudyArea $requestStudyArea,
    EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator)
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
