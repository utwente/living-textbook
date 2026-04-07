<?php

namespace App\UrlUtils\Model;

abstract class AbstractUrl
{
  protected string $url;

  /** AbstractUrl constructor. */
  public function __construct(string $url)
  {
    $this->url       = $url;
  }

  public function getUrl(): string
  {
    return $this->url;
  }
}
