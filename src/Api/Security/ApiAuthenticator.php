<?php

namespace App\Api\Security;

use App\Api\ApiErrorResponse;
use App\Entity\UserApiToken;
use App\Repository\UserApiTokenRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;

class ApiAuthenticator extends AbstractGuardAuthenticator
{
  private const API_TOKEN_HEADER = 'X-LTB-AUTH';
  private UserApiTokenRepository $userApiTokenRepository;
  private UserPasswordEncoderInterface $userPasswordEncoder;
  private EntityManagerInterface $entityManager;

  public function __construct(
      UserApiTokenRepository       $userApiTokenRepository,
      UserPasswordEncoderInterface $userPasswordEncoder,
      EntityManagerInterface       $entityManager)
  {
    $this->userApiTokenRepository = $userApiTokenRepository;
    $this->userPasswordEncoder    = $userPasswordEncoder;
    $this->entityManager          = $entityManager;
  }

  public function start(Request $request, AuthenticationException $authException = NULL)
  {
    return $this->createUnauthorizedResponse(
        sprintf('Make sure to provide the %s header', self::API_TOKEN_HEADER)
    );
  }

  public function supports(Request $request)
  {
    return $request->headers->has(self::API_TOKEN_HEADER);
  }

  public function getCredentials(Request $request)
  {
    // Split token into user id and token
    $token = $request->headers->get(self::API_TOKEN_HEADER);

    [$tokenId, $authToken] = explode('_', $token);

    return [
        'token_id' => $tokenId,
        'token'    => $authToken,
    ];
  }

  public function getUser($credentials, UserProviderInterface $userProvider)
  {
    return $this->userApiTokenRepository->findOneBy(['tokenId' => $credentials['token_id']]);
  }

  public function checkCredentials($credentials, UserInterface $user)
  {
    if (!$credentials['token'] || !$user instanceof UserApiToken) {
      throw new AuthenticationException('Missing token');
    }

    if ($user->getValidUntil() && $user->getValidUntil() < new DateTimeImmutable()) {
      throw new AuthenticationException('Token expired');
    }

    return $this->userPasswordEncoder->isPasswordValid($user, $credentials['token']);
  }

  public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
  {
    return $this->createUnauthorizedResponse('Invalid API credentials');
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
  {
    return NULL;
  }

  public function supportsRememberMe(): bool
  {
    return false;
  }

  private function createUnauthorizedResponse(string $description): JsonResponse
  {
    return new ApiErrorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED, $description);
  }

  public function createAuthenticatedToken(UserInterface $user, $providerKey): GuardTokenInterface
  {
    if (!$user instanceof UserApiToken) {
      throw new AuthenticationException('Invalid user class');
    }

    // Store last used
    $user->setLastUsed(new DateTimeImmutable());
    $this->entityManager->flush();

    // Create the token
    return parent::createAuthenticatedToken($user->getUser(), $providerKey);
  }
}
