<?php

namespace App\EntityHandler;

use App\Entity\LayoutConfigurationOverride;

class LayoutConfigurationOverrideHandler extends AbstractEntityHandler
{
  public function add(LayoutConfigurationOverride $override) {
    $this->validate($override);

    $this->em->persist($override);
    $this->em->flush();
  }

  public function update(LayoutConfigurationOverride $override) {
    $this->validate($override);
    $this->em->flush();
  }

  public function delete(LayoutConfigurationOverride $override) {
    if ($override->isDeleted()) {
      return;
    }

    $this->em->remove($override);
    $this->em->flush();
  }
}
