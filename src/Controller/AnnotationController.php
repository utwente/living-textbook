<?php

namespace App\Controller;

use App\Entity\Annotation;
use App\Entity\AnnotationComment;
use App\Entity\Concept;
use App\Entity\User;
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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AbbreviationController.
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/annotation", requirements={"_studyArea"="\d+"})
 */
class AnnotationController extends AbstractController
{
  /**
   * @Route("/{concept}/all", requirements={"concept"="\d+"}, options={"expose"="true"})
   *
   * @IsGranted("STUDYAREA_ANNOTATE", subject="requestStudyArea")
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
    $user = $this->getUser();
    assert($user instanceof User);
    $annotations = $annotationRepository->getForUserAndConcept($user, $concept);
    $json        = $serializer->serialize($annotations, 'json');

    return new JsonResponse($json, 200, [], true);
  }

  /**
   * @Route("/{concept}/add", requirements={"concept"="\d+"}, methods={"POST"}, options={"expose"="true"})
   *
   * @IsGranted("STUDYAREA_ANNOTATE", subject="requestStudyArea")
   *
   * @throws Exception
   *
   * @return JsonResponse
   */
  public function add(Request $request, RequestStudyArea $requestStudyArea, Concept $concept,
    ValidatorInterface $validator, EntityManagerInterface $em, SerializerInterface $serializer)
  {
    // Check study area
    $studyArea = $requestStudyArea->getStudyArea();
    if ($concept->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Parse version from request, must be either a DateTime or null
    $version = $request->request->get('version', null);
    if ($version !== null) {
      $version = new DateTime($version);
    }

    // Create new annotation
    $annotation = (new Annotation())
      ->setUser($this->getUser())
      ->setConcept($concept)
      ->setText($request->request->get('text', null))
      ->setContext($request->request->get('context', ''))
      ->setStart($request->request->getInt('start', 0))
      ->setEnd($request->request->getInt('end', 0))
      ->setSelectedText($request->request->get('selectedText', null))
      ->setVersion($version)
      ->setVisibility($request->request->get('visibility', Annotation::privateVisibility()));

    // Validate data
    if (null !== $result = $this->validate($annotation, $validator, $serializer)) {
      return $result;
    }

    // Save the entity
    $em->persist($annotation);
    $em->flush();

    return new JsonResponse($serializer->serialize($annotation, 'json'), 200, [], true);
  }

  /**
   * @Route("/{concept}/annotation/{annotation}/comment", requirements={"concept"="\d+", "annotation"="\d+"},
   *   methods={"POST"}, options={"expose"="true"})
   *
   * @IsGranted("STUDYAREA_ANNOTATE", subject="requestStudyArea")
   *
   * @return JsonResponse
   */
  public function addComment(Request $request, RequestStudyArea $requestStudyArea, Concept $concept, Annotation $annotation,
    ValidatorInterface $validator, EntityManagerInterface $em, SerializerInterface $serializer)
  {
    // Check study area/concept
    $studyArea = $requestStudyArea->getStudyArea();
    if ($concept->getStudyArea()->getId() != $studyArea->getId()
        || $annotation->getConceptId() != $concept->getId()) {
      throw $this->createNotFoundException();
    }

    $user = $this->getUser();
    assert($user instanceof User);

    // Validate whether this is a comment which is actually visible for the user
    if ($annotation->getVisibility() == Annotation::privateVisibility()
        && $annotation->getUserId() != $user->getId()) {
      // Only owner can reply on private comments
      throw $this->createAccessDeniedException();
    }

    if ($annotation->getUserId() != $user->getId()
        && $annotation->getVisibility() == Annotation::teacherVisibility()
        && !$studyArea->isEditable($user)) {
      // Only teachers can reply on teacher comments
      throw $this->createAccessDeniedException();
    }

    // everybody visibility is implied by method security

    // Create the comment
    $comment = (new AnnotationComment())
      ->setUser($user)
      ->setAnnotation($annotation)
      ->setText($request->request->get('text', null));

    // Validate data
    if (null !== $result = $this->validate($comment, $validator, $serializer)) {
      return $result;
    }

    // Save the entity
    $em->persist($comment);
    $em->flush();

    // Refresh the data
    $em->refresh($annotation);

    return new JsonResponse($serializer->serialize([
      'annotation' => $annotation,
      'comment'    => $comment,
    ], 'json'), 200, [], true);
  }

  /**
   * @Route("/{concept}/annotation/{annotation}/edit", requirements={"concept"="\d+", "annotation"="\d+"},
   *   methods={"POST"}, options={"expose"="true"})
   *
   * @IsGranted("STUDYAREA_ANNOTATE", subject="requestStudyArea")
   *
   * @return JsonResponse
   */
  public function editVisibility(Request $request, RequestStudyArea $requestStudyArea, Concept $concept, Annotation $annotation,
    ValidatorInterface $validator, EntityManagerInterface $em, SerializerInterface $serializer)
  {
    // Check study area/concept
    $studyArea = $requestStudyArea->getStudyArea();
    if ($concept->getStudyArea()->getId() != $studyArea->getId()
        || $annotation->getConceptId() != $concept->getId()) {
      throw $this->createNotFoundException();
    }

    // Validate rights
    $user = $this->getUser();
    assert($user instanceof User);
    if ($annotation->getUserId() != $user->getId()) {
      throw $this->createAccessDeniedException();
    }

    // Verify that there are no comments from other users yet
    if ($annotation->getCommentsFromOthersCount() > 0) {
      throw new BadRequestHttpException();
    }

    // Update annotation visibility
    $annotation->setVisibility($request->request->get('visibility', Annotation::privateVisibility()));

    // Validate data
    if (null !== $result = $this->validate($annotation, $validator, $serializer)) {
      return $result;
    }

    // Save the entity
    $em->persist($annotation);
    $em->flush();

    return new JsonResponse($serializer->serialize($annotation, 'json'), 200, [], true);
  }

  /**
   * @Route("/{concept}/annotation/{annotation}/remove", requirements={"concept"="\d+", "annotation"="\d+"},
   *   methods={"DELETE"}, options={"expose"="true"})
   *
   * @IsGranted("STUDYAREA_ANNOTATE", subject="requestStudyArea")
   *
   * @throws Exception
   *
   * @return JsonResponse
   */
  public function remove(RequestStudyArea $requestStudyArea, Concept $concept, Annotation $annotation, EntityManagerInterface $em)
  {
    // Check study area
    if ($concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()
        || $annotation->getConceptId() != $concept->getId()) {
      throw $this->createNotFoundException();
    }

    // Validate credentials
    $user = $this->getUser();
    assert($user instanceof User);
    if ($annotation->getUserId() != $user->getId() && !$this->isGranted('STUDYAREA_OWNER', $requestStudyArea)) {
      throw $this->createAccessDeniedException();
    }

    // Remove the annotation
    $em->remove($annotation);
    $em->flush();

    return new JsonResponse();
  }

  /**
   * @Route("/{concept}/annotation/{annotation}/comment/{comment}/remove",
   *   requirements={"concept"="\d+", "annotation"="\d+", "comment"="\d+"},
   *   methods={"DELETE"}, options={"expose"="true"})
   *
   * @IsGranted("STUDYAREA_ANNOTATE", subject="requestStudyArea")
   *
   * @return JsonResponse
   */
  public function removeComment(RequestStudyArea $requestStudyArea, Concept $concept, Annotation $annotation,
    AnnotationComment $comment, EntityManagerInterface $em, SerializerInterface $serializer)
  {
    // Check study area/concept/annotation
    $studyArea = $requestStudyArea->getStudyArea();
    if ($concept->getStudyArea()->getId() != $studyArea->getId()
        || $annotation->getConceptId() != $concept->getId()
        || $comment->getAnnotation()->getId() != $annotation->getId()) {
      throw $this->createNotFoundException();
    }

    // Validate credentials
    $user = $this->getUser();
    assert($user instanceof User);
    if ($comment->getUserId() != $user->getId() && !$this->isGranted('STUDYAREA_OWNER', $requestStudyArea)) {
      throw $this->createAccessDeniedException();
    }

    // Remove the comment
    $em->remove($comment);
    $em->flush();

    // Refresh data
    $em->refresh($annotation);

    // Return updated comment to update js state
    return new JsonResponse($serializer->serialize([
      'annotation' => $annotation,
    ], 'json'), 200, [], true);
  }

  /** @return JsonResponse|null */
  private function validate($object, ValidatorInterface $validator, SerializerInterface $serializer)
  {
    // Validate data
    $violations = $validator->validate($object);
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

    return null;
  }
}
