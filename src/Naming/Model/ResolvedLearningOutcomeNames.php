<?php

namespace App\Naming\Model;

use Override;
use Symfony\Component\String\Inflector\InflectorInterface;

use function strtolower;

class ResolvedLearningOutcomeNames implements ResolvedNamesInterface
{
  private string $obj;
  /** @var string */
  private $objs;

  public function __construct(string $obj)
  {
    $this->obj = strtolower($obj);
  }

  #[Override]
  public function resolvePlurals(InflectorInterface $inflector)
  {
    $this->objs = $inflector->pluralize($this->obj)[0];
  }

  public function obj(): string
  {
    return $this->obj;
  }

  public function objs(): string
  {
    return $this->objs;
  }
}
