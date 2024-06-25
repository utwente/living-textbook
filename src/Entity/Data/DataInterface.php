<?php

namespace App\Entity\Data;

interface DataInterface
{
  /** Determine whether this block has data. */
  public function hasData(): bool;
}
