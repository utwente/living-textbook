<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Repository\HelpRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;

/**
 * Each update will be saved as a new iteration of the help entity.
 */
#[ORM\Entity(repositoryClass: HelpRepository::class)]
#[ORM\Table]
class Help implements IdInterface
{
  use Blameable;
  use IdTrait;

  /** The help content. */
  #[ORM\Column(name: 'content', type: Types::TEXT, nullable: false)]
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
