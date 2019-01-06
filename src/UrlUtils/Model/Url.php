<?php

namespace App\UrlUtils\Model;

/**
 * Class Url
 *
 * Contains found URL metadata
 */
class Url extends AbstractUrl
{
  /** @var bool */
  private $isPath;

  /** @var bool */
  private $internal;

  /** @var UrlContext */
  private $context;

  public function __construct(string $url, bool $internal, UrlContext $context)
  {
    $internalPath = 0 === mb_stripos($url, '/');

    $this->internal  = $internal || $internalPath;
    $this->isPath    = $this->internal && $internalPath;
    $this->context   = $context;
    parent::__construct($url);
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
   * Get whether it is a path url (starts with /, and internal)
   *
   * @return bool
   */
  public function isPath(): bool
  {
    return $this->isPath;
  }

  /**
   * Get path part of the url
   *
   * @return string
   */
  public function getPath(): string
  {
    if ($this->isPath()) {
      return $this->getUrl();
    }

    // Source: http://www.ietf.org/rfc/rfc3986.txt
    $pattern = '/(?i)^(([^:\/?#]+):)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/';
    $matches = [];
    preg_match($pattern, $this->getUrl(), $matches);

    return $matches[5];
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
