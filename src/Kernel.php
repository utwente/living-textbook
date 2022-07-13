<?php

namespace App;

use Drenso\OidcBundle\Security\Factory\OidcFactory;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
  use MicroKernelTrait;

  /** @param ContainerBuilder $container */
  protected function build(ContainerBuilder $container)
  {
    // Register the Oidc factory
    $extension = $container->getExtension('security');
    assert($extension instanceof SecurityExtension);
    $extension->addSecurityListenerFactory(new OidcFactory()); /* @phan-suppress-current-line PhanDeprecatedFunction */
  }
}
