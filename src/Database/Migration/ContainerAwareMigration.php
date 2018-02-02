<?php

namespace App\Database\Migration;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerAwareMigration
{

  /** @var ContainerInterface */
  protected $container;

  /**
   * @param ContainerInterface|NULL $container
   */
  public function setContainer(ContainerInterface $container = NULL)
  {
    $this->container = $container;
  }
}
