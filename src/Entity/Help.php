<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;

/**
 * Class Help.
 *
 * Each update will be saved as a new iteration of the help entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\HelpRepository")
 */
class Help implements IdInterface
{
  use IdTrait;
  use Blameable;

  /**
   * The help content.
   *
   *
   * @ORM\Column(name="content", type="text", nullable=false)
   */
  private ?string $content = null;

  public function getContent(): string
  {
    return $this->content;
  }

  public function setContent(string $content): self
  {
    $this->content = $content;

    return $this;
  }
}
