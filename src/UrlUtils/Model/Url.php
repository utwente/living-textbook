<?php

namespace App\UrlUtils\Model;

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

  /** @var UrlContext */
  private $context;

  public function __construct(string $url, bool $internal, UrlContext $context)
  {
    $this->url      = $url;
    $this->internal = $internal;
    $this->context  = $context;
  }

  /**
   * Implementation to determine duplicates correctly
   * https://stackoverflow.com/questions/2426557/array-unique-for-objects
   */
  public function __toString()
  {
    return $this->url . '.' . ($this->internal ? '1' : '0') . '.' . $this->context->__toString();
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

  /**
   * @return UrlContext
   */
  public function getContext(): UrlContext
  {
    return $this->context;
  }
}
