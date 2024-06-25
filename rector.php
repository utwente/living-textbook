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
  ->withSets([
    \Rector\Symfony\Set\SymfonySetList::SYMFONY_54,
    \Rector\Symfony\Set\SymfonySetList::SYMFONY_60,
    \Rector\Symfony\Set\SymfonySetList::SYMFONY_61,
    \Rector\Symfony\Set\SymfonySetList::SYMFONY_62,
    \Rector\Symfony\Set\SymfonySetList::SYMFONY_63,
    \Rector\Symfony\Set\SymfonySetList::SYMFONY_64,
  ])
  ->withAttributesSets(symfony: true, doctrine: true, gedmo: true, jms: true)
  ->withConfiguredRule(\Rector\Php80\Rector\Class_\AnnotationToAttributeRector::class, [
    new AnnotationToAttribute(\App\Attribute\DenyOnFrozenStudyArea::class),
    new AnnotationToAttribute(\App\Validator\Constraint\Color::class),
    new AnnotationToAttribute(\App\Validator\Constraint\ConceptRelation::class),
    new AnnotationToAttribute(\App\Validator\Constraint\StudyAreaAccessType::class),
    new AnnotationToAttribute(\App\Validator\Constraint\Data\WordCount::class),
  ])
  ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml')
  ->withSymfonyContainerPhp(__DIR__ . '/tests/rector/symfony-container.php')
  ->withSkip([
    __DIR__ . '/src/Database/Traits/IdTrait.php', // @todo: Remove this when moving to attributes for Doctrine
    ReadOnlyPropertyRector::class, // Cannot be used with proxies yet (https://github.com/Ocramius/ProxyManager/issues/737)
    ClassPropertyAssignToConstructorPromotionRector::class, // Messes up the annotations
  ]);
