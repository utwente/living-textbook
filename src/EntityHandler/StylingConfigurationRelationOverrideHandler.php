<?php

namespace App\EntityHandler;

use App\Entity\StylingConfigurationRelationOverride;

class StylingConfigurationRelationOverrideHandler extends AbstractEntityHandler
{
  public function add(StylingConfigurationRelationOverride $override)
  {
    $this->validate($override);

    $this->em->persist($override);
    $this->em->flush();
  }

  public function update(StylingConfigurationRelationOverride $override)
  {
    $this->validate($override);
    $this->em->flush();
  }

  public function delete(StylingConfigurationRelationOverride $override)
  {
    if ($override->isDeleted()) {
      return;
    }

    $this->em->remove($override);
    $this->em->flush();
  }
}
