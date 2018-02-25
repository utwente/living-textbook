<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Data\DataExternalResources;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ExternalResource
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\ExternalResourceRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class ExternalResource
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var DataExternalResources|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataExternalResources", inversedBy="resources")
   * @ORM\JoinColumn(name="collection_id", referencedColumnName="id", nullable=false)
   */
  private $resourceCollection;

  /**
   * @var string
   * @ORM\Column(name="title", type="text", length=255, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=1, max=255)
   */
  private $title;

  /**
   * @var string
   *
   * @ORM\Column(name="description", type="text", length=1000, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=10, max=1000)
   */
  private $description;

  /**
   * @var string
   *
   * @ORM\Column(name="url", type="text", length=1000, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Url()
   * @Assert\Length(max=1000)
   */
  private $url;

  /**
   * @var bool
   *
   * @ORM\Column(name="broken", type="boolean", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $broken;

  /**
   * ExternalResource constructor.
   */
  public function __construct()
  {
    $this->title       = '';
    $this->description = '';
    $this->url         = '';
    $this->broken      = false;
  }

  /**
   * @return DataExternalResources|null
   */
  public function getResourceCollection(): ?DataExternalResources
  {
    return $this->resourceCollection;
  }

  /**
   * @param DataExternalResources $resourceCollection
   *
   * @return ExternalResource
   */
  public function setResourceCollection(DataExternalResources $resourceCollection): ExternalResource
  {
    $this->resourceCollection = $resourceCollection;

    return $this;
  }

  /**
   * @return string
   */
  public function getTitle(): string
  {
    return $this->title;
  }

  /**
   * @param string $title
   *
   * @return ExternalResource
   */
  public function setTitle(string $title): ExternalResource
  {
    $this->title = $title;

    return $this;
  }

  /**
   * @return string
   */
  public function getDescription(): string
  {
    return $this->description;
  }

  /**
   * @param string $description
   *
   * @return ExternalResource
   */
  public function setDescription(string $description): ExternalResource
  {
    $this->description = $description;

    return $this;
  }

  /**
   * @return string
   */
  public function getUrl(): string
  {
    return $this->url;
  }

  /**
   * @param string $url
   *
   * @return ExternalResource
   */
  public function setUrl(string $url): ExternalResource
  {
    $this->url = $url;

    return $this;
  }

  /**
   * @return bool
   */
  public function isBroken(): bool
  {
    return $this->broken;
  }

  /**
   * @param bool $broken
   *
   * @return ExternalResource
   */
  public function setBroken(bool $broken): ExternalResource
  {
    $this->broken = $broken;

    return $this;
  }
}
