<?php

namespace App\Database\Migration;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerAwareMigration
{
  /** @var ContainerInterface */
  protected $container;

  public function setContainer(?ContainerInterface $container = null)
  {
    $this->container = $container;
  }
}
