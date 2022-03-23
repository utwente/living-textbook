<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
  // Get parameters
  $parameters = $containerConfigurator->parameters();
  $parameters
      ->set(Option::PATHS, [__DIR__ . '/src'])
      ->set(Option::AUTO_IMPORT_NAMES, true)
      ->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_81)
      ->set(Option::SKIP, [
        __DIR__ . '/src/Database/Traits/IdTrait.php', // @todo: Remove this when moving to attributes for Doctrine
      ]);
};
