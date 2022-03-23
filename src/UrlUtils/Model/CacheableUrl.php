<?php

namespace App\UrlUtils\Model;

use DateTime;
use Exception;

class CacheableUrl extends AbstractUrl
{
  /** @var DateTime */
  private $timestamp;

  /**
   * CacheableUrl constructor.
   *
   * @throws Exception
   */
  public function __construct(string $url)
  {
    $this->timestamp = new DateTime();
    parent::__construct($url);
  }

  /** Timestamp when this URL was found, for checking purposes. */
  public function getTimestamp(): DateTime
  {
    return $this->timestamp;
  }

  /** Cache key for storing the object in a filesystem cache. */
  public function getCachekey(): string
  {
    return md5($this->getUrl());
  }

  /** @return CacheableUrl */
  public static function fromUrl(Url $url)
  {
    return new self($url->getUrl());
  }
}
