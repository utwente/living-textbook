<?php

namespace App\ExceptionHandler;

use Kickin\ExceptionHandlerBundle\Configuration\SymfonyMailerConfigurationInterface;
use Override;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

readonly class ExceptionHandlerConfiguration implements SymfonyMailerConfigurationInterface
{
  private bool $productionServer;

  private string $cacheDir;

  private string $exceptionSender;

  private string $exceptionReceiver;

  private string $appVersion;

  public function __construct(ParameterBagInterface $parameterBag)
  {
    $this->productionServer  = $parameterBag->get('production_server');
    $this->cacheDir          = $parameterBag->get('kernel.cache_dir');
    $this->exceptionSender   = $parameterBag->get('exception_sender');
    $this->exceptionReceiver = $parameterBag->get('exception_receiver');
    $this->appVersion        = sprintf('%s+%s', $parameterBag->get('app_version'), $parameterBag->get('commit_hash'));
  }

  #[Override]
  public function isProductionEnvironment(): bool
  {
    return $this->productionServer && $this->exceptionSender && $this->exceptionReceiver;
  }

  #[Override]
  public function getBacktraceFolder(): string
  {
    return $this->cacheDir . '/exception_handler';
  }

  #[Override]
  public function getSender(): Address
  {
    return new Address($this->exceptionSender, 'Living Textbook');
  }

  #[Override]
  public function getReceiver(): Address
  {
    return new Address($this->exceptionReceiver, 'Living Textbook');
  }

  #[Override]
  public function getUserInformation(?TokenInterface $token = null): string
  {
    if ($token !== null) {
      return $token->getUserIdentifier();
    }

    return 'No user (not authenticated)';
  }

  #[Override]
  public function getSystemVersion(): string
  {
    return $this->appVersion;
  }

  #[Override]
  public function filterCookieNames(): array
  {
    return ['LTB_SID'];
  }
}
