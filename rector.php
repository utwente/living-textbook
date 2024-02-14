<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\Config\RectorConfig;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rc): void {
  $rc->paths([__DIR__ . '/src']);
  $rc->importNames();
  $rc->phpVersion(PhpVersion::PHP_83);

  $rc->skip([
      __DIR__ . '/src/Database/Traits/IdTrait.php', // @todo: Remove this when moving to attributes for Doctrine
      ReadOnlyPropertyRector::class, // Cannot be used with proxies yet (https://github.com/Ocramius/ProxyManager/issues/737)
      ReturnNeverTypeRector::class, // Not working properly
      ClassPropertyAssignToConstructorPromotionRector::class, // Messes up the annotations
      ArraySpreadInsteadOfArrayMergeRector::class, // Not very useful, leads to Phan issue
      JsonThrowOnErrorRector::class, // Disable to not change functionality
  ]);

  $rc->import(LevelSetList::UP_TO_PHP_81);
};
