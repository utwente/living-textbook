<?php

namespace App\Api\Controller;

use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Entity\StudyArea;
use App\Entity\User;
use App\Request\Wrapper\RequestStudyArea;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\SerializerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractController
{
  protected SerializerInterface $serializer;
  private SerializationContextFactoryInterface $contextFactory;

  public function __construct(
      SerializerInterface $serializer, SerializationContextFactoryInterface $contextFactory)
  {
    $this->serializer     = $serializer;
    $this->contextFactory = $contextFactory;
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

  protected function getUser(): User
  {
    $user = parent::getUser();
    if (!$user instanceof User) {
      throw new RuntimeException(sprintf('Unsupported user %s', get_class($user)));
    }

    return $user;
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
}
