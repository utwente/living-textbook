<?php

namespace App\Entity\Data;

interface TextDataInterface extends DataInterface
{
  public function getText(): ?string;
}
