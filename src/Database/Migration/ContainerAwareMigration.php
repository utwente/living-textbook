<?php

namespace App\Database\Migration;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerAwareMigration
{
  protected ?ContainerInterface $container = null;

  public function setContainer(?ContainerInterface $container = null): void
  {
    $this->container = $container;
  }
}
