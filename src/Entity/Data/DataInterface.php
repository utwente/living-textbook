<?php

namespace App\Entity\Data;

/**
 * Interface DataInterface.
 */
interface DataInterface
{
  /** Determine whether this block has data. */
  public function hasData(): bool;
}
