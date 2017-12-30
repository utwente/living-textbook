<?php

namespace App\ExceptionHandler;

use Kickin\ExceptionHandlerBundle\Configuration\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ExceptionHandlerConfiguration implements ConfigurationInterface
{

  /**
   * @var ContainerInterface
   */
  private $container;

  /**
   * @inheritdoc
   */
  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  /**
   * Indicate whether the current environment is a production environment
   *
   * @return bool
   */
  public function isProductionEnvironment()
  {
    return $this->container->getParameter('production_server');
  }

  /**
   * Return the backtrace file root folder path
   *
   * @return string
   */
  public function getBacktraceFolder()
  {
    return $this->container->getParameter('kernel.cache_dir') . '/exception_handler';
  }

  /**
   * SwiftMailer representation of the error sender
   *
   * @return string|array
   */
  public function getSender()
  {
    return array('helpdesk@snt.utwente.nl' => 'SNT WESP');
  }

  /**
   * SwiftMailer representation of the error receiver
   *
   * @return mixed
   */
  public function getReceiver()
  {
    return array($this->container->getParameter('exception_receiver') => 'Living Textbook');
  }

  /**
   * Retrieve user information from the token, and return it in a single string
   *
   * @param TokenInterface|null $token
   *
   * @return string
   */
  public function getUserInformation(TokenInterface $token = null)
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
    return $this->container->getParameter('app_version');
  }
}
