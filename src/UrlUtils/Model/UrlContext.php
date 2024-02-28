<?php

namespace App\UrlUtils\Model;

use InvalidArgumentException;
use Override;
use Stringable;

class UrlContext implements Stringable
{
  /** @var string Class name */
  private string $class;

  /** @var int Class id */
  private int $id;

  /** @var string Class path */
  private string $path;

  private bool $inline;

  public function __construct(string $class, int $id = -1, string $path = '')
  {
    if ($class === null) {
      throw new InvalidArgumentException('Class cannot be null');
    }

    $this->class  = $class;
    $this->id     = $id ?? -1;
    $this->path   = $path ?? '';
    $this->inline = false;
  }

  /**
   * Implementation to determine duplicates correctly
   * https://stackoverflow.com/questions/2426557/array-unique-for-objects.
   */
  #[Override]
  public function __toString(): string
  {
    return $this->class . '.' . $this->path . '.' . ($this->inline ? '1' : '0');
  }

  /**
   * Set context as inline.
   *
   * @return UrlContext New context instance
   */
  public function asInline(): UrlContext
  {
    $new         = new self($this->class, $this->id, $this->path);
    $new->inline = true;

    return $new;
  }

  public function getClass(): string
  {
    return $this->class;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getPath(): string
  {
    return $this->path;
  }

  /** Change the camelCase of the property path to snake-case, as that is used in the translations. */
  public function getPathAsTransKey(): string
  {
    return strtolower((string)preg_replace('/(?<!^)[A-Z]/', '-$0', $this->getPath()));
  }

  public function isInline(): bool
  {
    return $this->inline;
  }
}
