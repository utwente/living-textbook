<?php

namespace App\Api\Security;

use App\Api\ApiErrorResponse;
use App\Entity\UserApiToken;
use App\Repository\UserApiTokenRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

class ApiAuthenticator extends AbstractAuthenticator
{
  final public const API_TOKEN_HEADER = 'X-LTB-AUTH';

  public function __construct(
    private readonly UserApiTokenRepository $userApiTokenRepository,
    private readonly UserPasswordHasherInterface $passwordHasher,
    private readonly EntityManagerInterface $entityManager)
  {
  }

  #[Override]
  public function supports(Request $request): bool
  {
    return $request->headers->has(self::API_TOKEN_HEADER);
  }

  #[Override]
  public function authenticate(Request $request): Passport
  {
    // Split token into user id and token
    $token = (string)$request->headers->get(self::API_TOKEN_HEADER);

    $tokenData = explode('_', $token);

    if (count($tokenData) !== 2) {
      throw new BadCredentialsException();
    }

    return new Passport(
      new UserBadge(
        $tokenData[0],
        fn ($userIdentifier): ?UserApiToken => $this->userApiTokenRepository->findOneBy(['tokenId' => $userIdentifier])
      ),
      new CustomCredentials(
        fn (string $password, UserApiToken $apiToken): bool => (!$apiToken->getValidUntil() || $apiToken->getValidUntil() > new DateTimeImmutable())
        && $this->passwordHasher->isPasswordValid($apiToken, $password),
        $tokenData[1]
      ),
      [
        new PasswordUpgradeBadge($tokenData[1], $this->userApiTokenRepository),
      ]
    );
  }

  #[Override]
  public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
  {
    return $this->createUnauthorizedResponse('Invalid API credentials');
  }

  #[Override]
  public function onAuthenticationSuccess(Request $request, TokenInterface $token, $firewallName): ?Response
  {
    return null;
  }

  private function createUnauthorizedResponse(string $description): JsonResponse
  {
    return new ApiErrorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED, $description);
  }

  #[Override]
  public function createToken(Passport $passport, $firewallName): TokenInterface
  {
    $apiToken = $passport->getUser();
    if (!$apiToken instanceof UserApiToken) {
      throw new AuthenticationException('Invalid user class');
    }

    // Store last used
    $apiToken->setLastUsed(new DateTimeImmutable());
    $this->entityManager->flush();

    // Create the token
    return new PostAuthenticationToken($apiToken->getUser(), $firewallName, $apiToken->getUser()->getRoles());
  }
}
