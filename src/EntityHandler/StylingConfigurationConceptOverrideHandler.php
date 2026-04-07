<?php

namespace App\EntityHandler;

use App\Entity\StylingConfigurationConceptOverride;

class StylingConfigurationConceptOverrideHandler extends AbstractEntityHandler
{
  public function add(StylingConfigurationConceptOverride $override): void
  {
    $this->validate($override);

    $this->em->persist($override);
    $this->em->flush();
  }

  public function update(StylingConfigurationConceptOverride $override): void
  {
    $this->validate($override);
    $this->em->flush();
  }

  public function delete(StylingConfigurationConceptOverride $override): void
  {
    if ($override->isDeleted()) {
      return;
    }

    $this->em->remove($override);
    $this->em->flush();
  }
}
