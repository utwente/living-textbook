<?php

namespace App\UrlUtils\Model;

use Override;
use Stringable;

/**
 * Class Url.
 *
 * Contains found URL metadata
 */
class Url extends AbstractUrl implements Stringable
{
  private bool $isPath;

  private bool $internal;

  private UrlContext $context;

  public function __construct(string $url, bool $internal, UrlContext $context)
  {
    $internalPath = 0 === mb_stripos($url, '/');

    $this->internal = $internal || $internalPath;
    $this->isPath   = $this->internal && $internalPath;
    $this->context  = $context;
    // Add http scheme to non-internal URLs for better path detection
    if (!$this->internal && 0 === preg_match('(://)', $url)) {
      $url = 'http://' . $url;
    }
    parent::__construct($url);
  }

  /**
   * Implementation to determine duplicates correctly
   * https://stackoverflow.com/questions/2426557/array-unique-for-objects.
   */
  #[Override]
  public function __toString(): string
  {
    return $this->url . '.' . ($this->internal ? '1' : '0') . '.' . $this->context->__toString();
  }

  /** Get parts of an URL, see http://www.ietf.org/rfc/rfc3986.txt. */
  protected function getUrlParts(): array
  {
    $pattern = '/(?i)^(([^:\/?#]+):)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/';
    $matches = [];
    preg_match($pattern, $this->getUrl(), $matches);

    return $matches;
  }

  /** Get whether it is a path url (starts with /, and internal). */
  public function isPath(): bool
  {
    return $this->isPath;
  }

  /** Get path part of the url. */
  public function getPath(): string
  {
    if ($this->isPath()) {
      return $this->getUrl();
    }

    return $this->getUrlParts()[5];
  }

  /** Get host part of the url. */
  public function getHost(): string
  {
    if ($this->isPath()) {
      return '';
    }

    return $this->getUrlParts()[4];
  }

  public function isInternal(): bool
  {
    return $this->internal;
  }

  public function getContext(): UrlContext
  {
    return $this->context;
  }
}
