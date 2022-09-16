<?php

namespace App\ExceptionHandler;

use Kickin\ExceptionHandlerBundle\Configuration\SymfonyMailerConfigurationInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ExceptionHandlerConfiguration implements SymfonyMailerConfigurationInterface
{
  /** @var bool */
  private $productionServer;

  /** @var string */
  private $cacheDir;

  /** @var string */
  private $exceptionSender;

  /** @var string */
  private $exceptionReceiver;

  /** @var string */
  private $appVersion;

  public function __construct(ParameterBagInterface $parameterBag)
  {
    $this->productionServer  = $parameterBag->get('production_server');
    $this->cacheDir          = $parameterBag->get('kernel.cache_dir');
    $this->exceptionSender   = $parameterBag->get('exception_sender');
    $this->exceptionReceiver = $parameterBag->get('exception_receiver');
    $this->appVersion        = sprintf('%s+%s', $parameterBag->get('app_version'), $parameterBag->get('commit_hash'));
  }

  /** {@inheritdoc} */
  public function isProductionEnvironment(): bool
  {
    return $this->productionServer && $this->exceptionSender && $this->exceptionReceiver;
  }

  /** {@inheritdoc} */
  public function getBacktraceFolder(): string
  {
    return $this->cacheDir . '/exception_handler';
  }

  /** {@inheritdoc} */
  public function getSender()
  {
    return new Address($this->exceptionSender, 'Living Textbook');
  }

  /** {@inheritdoc} */
  public function getReceiver()
  {
    return new Address($this->exceptionReceiver, 'Living Textbook');
  }

  /** {@inheritdoc} */
  public function getUserInformation(TokenInterface $token = null): string
  {
    if ($token !== null) {
      return $token->getUserIdentifier();
    }

    return 'No user (not authenticated)';
  }

  /** {@inheritdoc} */
  public function getSystemVersion(): string
  {
    return $this->appVersion;
  }
}
