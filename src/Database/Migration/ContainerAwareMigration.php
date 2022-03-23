<?php

namespace App\Database\Migration;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerAwareMigration
{
  /** @var ContainerInterface */
  protected $container;

  /** @param ContainerInterface|null $container */
  public function setContainer(ContainerInterface $container = null)
  {
    $this->container = $container;
  }
}
