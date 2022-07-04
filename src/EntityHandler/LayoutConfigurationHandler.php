<?php

namespace App\EntityHandler;

use App\Entity\LayoutConfiguration;

class LayoutConfigurationHandler extends AbstractEntityHandler
{
  public function add(LayoutConfiguration $layoutConfiguration): void
  {
    $this->validate($layoutConfiguration);

    $this->em->persist($layoutConfiguration);
    $this->em->flush();
  }

  public function update(LayoutConfiguration $layoutConfiguration): void
  {
    $this->validate($layoutConfiguration);

    $this->em->flush();
  }

  public function delete(LayoutConfiguration $layoutConfiguration): void
  {
    if ($layoutConfiguration->isDeleted()) {
      return;
    }

    $this->em->remove($layoutConfiguration);
    $this->em->flush();
  }
}
