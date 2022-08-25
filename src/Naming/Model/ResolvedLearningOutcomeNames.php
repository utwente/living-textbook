<?php

namespace App\Naming\Model;

use Symfony\Component\String\Inflector\InflectorInterface;

class ResolvedLearningOutcomeNames implements ResolvedNamesInterface
{
  /** @var string */
  private $obj;
  /** @var string */
  private $objs;

  public function __construct(string $obj)
  {
    $this->obj = strtolower($obj);
  }

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
