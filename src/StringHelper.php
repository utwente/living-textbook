<?php

namespace App;

class StringHelper
{
  /**
   * Converts an empty string to NULL
   */
  public static function emptyToNull(?string $value): ?string
  {
    $value = trim($value);

    return $value === '' ? NULL : $value;
  }
}
