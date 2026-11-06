<?php

namespace App\Naming\Model;

use Doctrine\Inflector\Inflector;
use Override;
use Symfony\Component\String\Inflector\InflectorInterface;

use function Symfony\Component\String\u;

class ResolvedTermNames implements ResolvedNamesInterface
{
  const string DEPRECATED = '(deprecated)';
  private readonly string $obj;
  private string          $objs = '';

  public function __construct(string $obj)
  {
    $this->obj = u($obj)->lower();
  }

  #[Override]
  public function resolvePlurals(InflectorInterface $inflector): void
  {
    $term = u($this->obj);
    if ($deprecatedTerm = $term->endsWith(self::DEPRECATED)) {
      $term = $term->trimSuffix(self::DEPRECATED)->trimEnd();
    }

    $this->objs = $inflector->pluralize($term)[0];

    if ($deprecatedTerm) {
      $this->objs .= ' ' . self::DEPRECATED;
    }
  }

  public function obj(bool $titleCase = false): string
  {
    return $titleCase ? u($this->obj)->title() : $this->obj;
  }

  public function objs(bool $titleCase = false): string
  {
    return $titleCase ? u($this->objs)->title() : $this->objs;
  }
}
