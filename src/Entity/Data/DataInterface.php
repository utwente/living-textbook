<?php

namespace App\Entity\Data;

/**
 * Interface DataInterface
 *
 * @package App\Entity\Data
 */
interface DataInterface
{
  /**
   * Determine whether this block has data
   *
   * @return boolean
   */
  function hasData() : bool;
}
