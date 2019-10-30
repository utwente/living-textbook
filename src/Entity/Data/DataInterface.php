<?php

namespace App\Entity\Data;

use App\Entity\Contracts\ReviewableInterface;

/**
 * Interface DataInterface
 *
 * @package App\Entity\Data
 */
interface DataInterface extends ReviewableInterface
{
  /**
   * Determine whether this block has data
   *
   * @return boolean
   */
  function hasData(): bool;
}
