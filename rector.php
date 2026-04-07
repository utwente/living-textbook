<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;

return RectorConfig::configure()
  ->withCache('./var/cache/rector', FileCacheStorage::class)
  ->withPaths([__DIR__ . '/src'])
  ->withImportNames()
  ->withParallel(timeoutSeconds: 180, jobSize: 10)
  ->withPhpSets()
  ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml')
  ->withSymfonyContainerPhp(__DIR__ . '/tests/rector/symfony-container.php')
  ->withPreparedSets(
    typeDeclarations: true,
  )
  ->withComposerBased(
    twig: true,
    doctrine: true,
    phpunit: true,
    symfony: true,
  )
  ->withSkip([
    ClassPropertyAssignToConstructorPromotionRector::class, // Messes up the annotations
  ]);
