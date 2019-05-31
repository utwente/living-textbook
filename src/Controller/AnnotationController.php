<?php

namespace App\Controller;

use App\Entity\Annotation;
use App\Entity\Concept;
use App\Repository\AnnotationRepository;
use App\Request\Wrapper\RequestStudyArea;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AbbreviationController
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/annotation", requirements={"_studyArea"="\d+"})
 */
class AnnotationController extends AbstractController
{

  /**
   * @Route("/{concept}/all", requirements={"concept"="\d+"}, options={"expose"="true"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea     $requestStudyArea
   * @param Concept              $concept
   * @param AnnotationRepository $annotationRepository
   * @param SerializerInterface  $serializer
   *
   * @return JsonResponse
   */
  public function all(RequestStudyArea $requestStudyArea, Concept $concept,
                      AnnotationRepository $annotationRepository, SerializerInterface $serializer)
  {
    // Check study area
    $studyArea = $requestStudyArea->getStudyArea();
    if ($concept->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Determine whether the user has "teacher" role
    $user        = $this->getUser();
    $annotations = $annotationRepository->getForUserAndConcept($user, $studyArea->isEditable($user), $concept);
    $json        = $serializer->serialize($annotations, 'json');

    return new JsonResponse($json, 200, [], true);
  }

  /**
   * @Route("/{concept}/add", requirements={"concept"="\d+"}, methods={"POST"}, options={"expose"="true"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Concept                $concept
   * @param ValidatorInterface     $validator
   * @param EntityManagerInterface $em
   * @param SerializerInterface    $serializer
   *
   * @return JsonResponse
   * @throws Exception
   */
  public function add(Request $request, RequestStudyArea $requestStudyArea, Concept $concept,
                      ValidatorInterface $validator, EntityManagerInterface $em, SerializerInterface $serializer)
  {
    // Check study area
    if ($concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    // Parse version from request, must be either a DateTime or null
    $version = $request->request->get('version', NULL);
    if ($version !== NULL) {
      $version = new DateTime($version);
    }

    // Create new annotation
    $annotation = (new Annotation())
        ->setUser($this->getUser())
        ->setConcept($concept)
        ->setText($request->request->get('text', NULL))
        ->setContext($request->request->get('context', ''))
        ->setStart($request->request->getInt('start', 0))
        ->setEnd($request->request->getInt('end', 0))
        ->setSelectedText($request->request->get('selectedText', NULL))
        ->setVersion($version)
        ->setVisibility($request->request->get('visibility', Annotation::privateVisibility()));

    // Validate data
    $violations = $validator->validate($annotation);
    if (count($violations) > 0) {
      $errors = [];
      foreach ($violations as $violation) {
        $errors[] = [
            'message' => $violation->getMessage(),
            'path'    => $violation->getPropertyPath(),
        ];
      }

      return new JsonResponse($serializer->serialize($errors, 'json'), 400, [], true);
    }

    // Save the entity
    $em->persist($annotation);
    $em->flush();

    return new JsonResponse($serializer->serialize($annotation, 'json'), 200, [], true);
  }

  /**
   * @Route("/{concept}/annotation/{annotation}/edit", requirements={"concept"="\d+", "annotation"="\d+"},
   *   methods={"POST"}, options={"expose"="true"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Concept                $concept
   * @param Annotation             $annotation
   * @param ValidatorInterface     $validator
   * @param EntityManagerInterface $em
   * @param SerializerInterface    $serializer
   *
   * @return JsonResponse
   */
  public function editVisibility(Request $request, RequestStudyArea $requestStudyArea, Concept $concept, Annotation $annotation,
                                 ValidatorInterface $validator, EntityManagerInterface $em, SerializerInterface $serializer)
  {
    // Check study area/concept
    if ($annotation->getConceptId() != $concept->getId() ||
        $concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    // Validate rights
    if ($annotation->getUserId() != $this->getUser()->getId()) {
      throw $this->createAccessDeniedException();
    }

    // Update annotation visibility
    $annotation->setVisibility($request->request->get('visibility', Annotation::privateVisibility()));

    // Validate data
    $violations = $validator->validate($annotation);
    if (count($violations) > 0) {
      $errors = [];
      foreach ($violations as $violation) {
        $errors[] = [
            'message' => $violation->getMessage(),
            'path'    => $violation->getPropertyPath(),
        ];
      }

      return new JsonResponse($serializer->serialize($errors, 'json'), 400, [], true);
    }

    // Save the entity
    $em->persist($annotation);
    $em->flush();

    return new JsonResponse($serializer->serialize($annotation, 'json'), 200, [], true);
  }

  /**
   * @Route("/{concept}/annotation/{annotation}/remove", requirements={"concept"="\d+", "annotation"="\d+"},
   *   methods={"DELETE"}, options={"expose"="true"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea       $requestStudyArea
   * @param Concept                $concept
   * @param Annotation             $annotation
   * @param EntityManagerInterface $em
   *
   * @return JsonResponse
   * @throws Exception
   */
  public function remove(RequestStudyArea $requestStudyArea, Concept $concept, Annotation $annotation, EntityManagerInterface $em)
  {
    // Check study area
    if ($concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    // Validate credentials
    if ($annotation->getUserId() != $this->getUser()->getId()) {
      throw $this->createAccessDeniedException();
    }

    // Save the entity
    $em->remove($annotation);
    $em->flush();

    return new JsonResponse();
  }
}
