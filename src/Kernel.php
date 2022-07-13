<?php

namespace App;

use Doctrine\Common\Annotations\AnnotationReader;
use Drenso\OidcBundle\Security\Factory\OidcFactory;
use Exception;
use Generator;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
  use MicroKernelTrait;
  public const CONFIG_EXTS = '.{php,xml,yaml,yml}';

  public function __construct(string $environment, bool $debug)
  {
    parent::__construct($environment, $debug);

    AnnotationReader::addGlobalIgnoredName('suppress');
  }

  /** @return string */
  public function getCacheDir()
  {
    return $this->getProjectDir() . '/var/cache/' . $this->environment;
  }

  /** @return string */
  public function getLogDir()
  {
    return $this->getProjectDir() . '/var/log';
  }

  /** @return Generator|BundleInterface[] */
  public function registerBundles()
  {
    /** @noinspection PhpIncludeInspection */
    $contents = require $this->getProjectDir() . '/config/bundles.php';
    foreach ($contents as $class => $envs) {
      if (isset($envs['all']) || isset($envs[$this->environment])) {
        yield new $class();
      }
    }
  }

  /** @throws Exception */
  protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
  {
    $container->setParameter('container.autowiring.strict_mode', true);
    $container->setParameter('container.dumper.inline_class_loader', true);
    $confDir = $this->getProjectDir() . '/config';
    $loader->load($confDir . '/packages/*' . self::CONFIG_EXTS, 'glob');
    if (is_dir($confDir . '/packages/' . $this->environment)) {
      $loader->load($confDir . '/packages/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
    }
    $loader->load($confDir . '/services' . self::CONFIG_EXTS, 'glob');
    $loader->load($confDir . '/services_' . $this->environment . self::CONFIG_EXTS, 'glob');
  }

  protected function configureRoutes(RouteCollectionBuilder $routes)
  {
    $confDir = $this->getProjectDir() . '/config';
    if (is_dir($confDir . '/routes/')) {
      $routes->import($confDir . '/routes/*' . self::CONFIG_EXTS, '/', 'glob');
    }
    if (is_dir($confDir . '/routes/' . $this->environment)) {
      $routes->import($confDir . '/routes/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
    }
    $routes->import($confDir . '/routes' . self::CONFIG_EXTS, '/', 'glob');
  }

  /** @param ContainerBuilder $container */
  protected function build(ContainerBuilder $container)
  {
    // Register the Oidc factory
    $extension = $container->getExtension('security');
    assert($extension instanceof SecurityExtension);
    $extension->addSecurityListenerFactory(new OidcFactory()); /* @phan-suppress-current-line PhanDeprecatedFunction */
  }
}
