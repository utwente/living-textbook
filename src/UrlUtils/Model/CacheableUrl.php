<?php

namespace App\UrlUtils\Model;

class CacheableUrl extends AbstractUrl
{

  /** @var \DateTime */
  private $timestamp;

  /**
   * CacheableUrl constructor.
   *
   * @param string $url
   */
  public function __construct(string $url)
  {
    $this->timestamp = new \DateTime();
    parent::__construct($url);
  }

  /**
   * Timestamp when this URL was found, for checking purposes
   *
   * @return \DateTime
   */
  public function getTimestamp(): \DateTime
  {
    return $this->timestamp;
  }

  /**
   * Cache key for storing the object in a filesystem cache
   *
   * @return string
   */
  public function getCachekey(): string
  {
    return md5($this->getUrl());
  }

  /**
   * @param Url $url
   *
   * @return CacheableUrl
   */
  public static function fromUrl(Url $url)
  {
    return new self($url->getUrl());
  }

}