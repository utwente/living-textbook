<?php

namespace App\Entity\Contracts;

interface SearchableInterface
{
  /**
   * Searches in the object on the given search, returns an array with search result metadata
   * '_data'   => the object,
   * '_title'  => object title,
   * 'results' => ['prio' => result priority, 'property' => object property for result, 'data' => result data].
   */
  public function searchIn(string $search): array;
}
