<?php

namespace App\EntityHandler;

use App\Entity\StylingConfiguration;

class StylingConfigurationHandler extends AbstractEntityHandler
{
  public function add(StylingConfiguration $stylingConfiguration): void
  {
    $this->validate($stylingConfiguration);

    $this->em->persist($stylingConfiguration);
    $this->em->flush();
  }

  public function update(StylingConfiguration $stylingConfiguration): void
  {
    $this->validate($stylingConfiguration);

    $this->em->flush();
  }

  public function delete(StylingConfiguration $stylingConfiguration): void
  {
    if ($stylingConfiguration->isDeleted()) {
      return;
    }

    $this->em->remove($stylingConfiguration);
    $this->em->flush();
  }
}
