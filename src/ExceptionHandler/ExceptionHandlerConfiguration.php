<?php

namespace App\ExceptionHandler;

use Kickin\ExceptionHandlerBundle\Configuration\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ExceptionHandlerConfiguration implements ConfigurationInterface
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

  /**
   * @inheritdoc
   */
  public function __construct(ParameterBagInterface $parameterBag)
  {
    $this->productionServer  = $parameterBag->get('production_server');
    $this->cacheDir          = $parameterBag->get('kernel.cache_dir');
    $this->exceptionSender   = $parameterBag->get('exception_sender');
    $this->exceptionReceiver = $parameterBag->get('exception_receiver');
    $this->appVersion        = sprintf('%s+%s', $parameterBag->get('app_version'), $parameterBag->get('commit_hash'));

  }

  /**
   * Indicate whether the current environment is a production environment
   *
   * @return bool
   */
  public function isProductionEnvironment()
  {
    return $this->productionServer;
  }

  /**
   * Return the backtrace file root folder path
   *
   * @return string
   */
  public function getBacktraceFolder()
  {
    return $this->cacheDir . '/exception_handler';
  }

  /**
   * SwiftMailer representation of the error sender
   *
   * @return string|array
   */
  public function getSender()
  {
    return array($this->exceptionSender => 'Living Textbook');
  }

  /**
   * SwiftMailer representation of the error receiver
   *
   * @return mixed
   */
  public function getReceiver()
  {
    return array($this->exceptionReceiver => 'Living Textbook');
  }

  /**
   * Retrieve user information from the token, and return it in a single string
   *
   * @param TokenInterface|null $token
   *
   * @return string
   */
  public function getUserInformation(TokenInterface $token = NULL)
  {
    if ($token !== NULL) {
      return $token->getUsername();
    }

    return 'No user (not authenticated)';
  }

  /**
   * Retrieve the system version
   *
   * @return mixed
   */
  public function getSystemVersion()
  {
    return $this->appVersion;
  }
}
