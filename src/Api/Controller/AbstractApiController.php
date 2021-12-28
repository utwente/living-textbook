<?php

namespace App\Api\Controller;

use App\Entity\User;
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

  /**
   * @param mixed $data
   */
  protected function createDataResponse(
      $data,
      ?array $extraData = NULL,
      ?array $serializationGroups = NULL,
      int $statusCode = Response::HTTP_OK
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
}
