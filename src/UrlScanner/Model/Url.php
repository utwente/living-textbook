<?php

namespace App\UrlScanner\Model;

/**
 * Class Url
 *
 * Contains found URL metadata
 */
class Url
{
  /** @var string */
  private $url;

  /** @var bool */
  private $internal;

  public function __construct(string $url, bool $internal)
  {
    $this->url      = $url;
    $this->internal = $internal;
  }

  /**
   * @return string
   */
  public function getUrl(): string
  {
    return $this->url;
  }

  /**
   * @return bool
   */
  public function isInternal(): bool
  {
    return $this->internal;
  }
}
