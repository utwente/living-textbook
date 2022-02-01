<?php

namespace App\Api\Controller;

use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Entity\StudyArea;
use App\Entity\User;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\SerializerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractApiController extends AbstractController
{
  public function __construct(
      protected SerializerInterface $serializer,
      protected EntityManagerInterface $em,
      protected ValidatorInterface $validator,
      private readonly SerializationContextFactoryInterface $contextFactory)
  {
  }

  protected function assertStudyAreaObject(
      StudyArea|RequestStudyArea $studyArea,
      StudyAreaFilteredInterface $object): void
  {
    if ($studyArea instanceof RequestStudyArea) {
      $studyArea = $studyArea->getStudyArea();
    }

    if ($studyArea->getId() !== $object->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }
  }

  protected function createDataResponse(
      mixed  $data,
      ?array $extraData = NULL,
      ?array $serializationGroups = NULL,
      int    $statusCode = Response::HTTP_OK
  ): JsonResponse
  {
    $payload         = $extraData ?? [];
    $payload['data'] = $data;

    $context = $this->contextFactory->createSerializationContext();
    $context->setGroups($serializationGroups ?? ['Default']);

    return JsonResponse::fromJsonString($this->serializer->serialize($payload, 'json', $context), $statusCode);
  }

  /**
   * Retrieve the data from the request body.
   *
   * @template T
   *
   * @param class-string<T> $class
   *
   * @return T The requested type
   */
  protected function getTypedFromBody(Request $request, string $class)
  {
    return $this->getUntypedFromBody($request, $class);
  }

  /**
   * Retrieve the untyped data from the request body.
   */
  protected function getUntypedFromBody(Request $request, string $type): mixed
  {
    try {
      return $this->serializer->deserialize($request->getContent(), $type, 'json');
    } catch (Exception $e) {
      throw new BadRequestHttpException('Deserialization failed!', $e);
    }
  }

  protected function getUser(): User
  {
    $user = parent::getUser();
    if (!$user instanceof User) {
      throw new RuntimeException(sprintf('Unsupported user %s', get_class($user)));
    }

    return $user;
  }
}
