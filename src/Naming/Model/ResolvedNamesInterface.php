<?php

namespace App\Naming\Model;

use Symfony\Component\String\Inflector\InflectorInterface;

interface ResolvedNamesInterface
{
  public function resolvePlurals(InflectorInterface $inflector): void;
}
