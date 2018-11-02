<?php

namespace App\UrlUtils\Model;

class UrlContext
{
  /** @var string Class name */
  private $class;

  /** @var string Class path */
  private $path;

  /** @var bool */
  private $inline;

  public function __construct(string $class, string $path = '')
  {
    if ($class === NULL) {
      throw new \InvalidArgumentException("Class cannot be null");
    }

    $this->class  = $class;
    $this->path   = $path ?? '';
    $this->inline = false;
  }

  /**
   * Implementation to determine duplicates correctly
   * https://stackoverflow.com/questions/2426557/array-unique-for-objects
   */
  public function __toString()
  {
    return $this->class . '.' . $this->path . '.' . ($this->inline ? '1' : '0');
  }

  /**
   * Set context as inline
   *
   * @return UrlContext New context instance
   */
  public function asInline(): UrlContext
  {
    $new         = new self($this->class, $this->path);
    $new->inline = true;

    return $new;
  }

}
