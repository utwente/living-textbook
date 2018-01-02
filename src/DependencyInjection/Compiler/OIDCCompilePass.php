<?php

namespace App\DependencyInjection\Compiler;

use Doctrine\Common\Util\Debug;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OIDCCompilePass implements CompilerPassInterface
{

  /**
   * You can modify the container here before it is dumped to PHP code.
   *
   * @param ContainerBuilder $container
   */
  public function process(ContainerBuilder $container)
  {
    // Retrieve the well-known url from SurfConext
    $wellKnownUrl = $container->resolveEnvPlaceholders(
        $container->getParameter('oidc.well_known_url'),
        true // Resolve to actual values
    );

    // Retrieve the configuration data
    if (($response = file_get_contents($wellKnownUrl)) === false) {
      throw new \RuntimeException(sprintf("Could not retrieve SurfConext configuration from %s", $wellKnownUrl));
    }

    // Parse the configuration
    if (($config = json_decode($response, true)) === NULL) {
      throw new \RuntimeException(sprintf("Could not parse SurfConext configuration: %s", $response));
    };

    // Set the required information in the kernel as parameter
    $container->setParameter('oidc.userinfo-endpoint', $config['userinfo_endpoint']);
    $container->setParameter('oidc.authorization-endpoint', $config['authorization_endpoint']);
    $container->setParameter('oidc.token-endpoint', $config['token_endpoint']);
  }
}
