<?php

namespace App\Entity\Data;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\ExternalResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class DataExternalResources
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataExternalResourcesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataExternalResources implements DataInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var ExternalResource[]|ArrayCollection
   *
   * @ORM\OneToMany(targetEntity="App\Entity\ExternalResource", mappedBy="resourceCollection", cascade={"persist","remove"})
   * @ORM\OrderBy({"position" = "ASC"})
   *
   * @Assert\NotNull()
   * @Assert\Valid()
   */
  private $resources;

  /**
   * DataExternalResources constructor.
   */
  public function __construct()
  {
    $this->resources = new ArrayCollection();
  }

  /**
   * Determine whether this block has data
   *
   * @return boolean
   */
  function hasData(): bool
  {
    return count($this->resources) > 0;
  }

  /**
   * @return ArrayCollection|ExternalResource[]
   */
  public function getResources()
  {
    return $this->resources;
  }

  /**
   * @param ExternalResource $externalResource
   *
   * @return $this
   */
  public function addResource(ExternalResource $externalResource): DataExternalResources
  {
    // Check whether the source is set, otherwise set it as this
    if (!$externalResource->getResourceCollection()) {
      $externalResource->setResourceCollection($this);
    }

    $this->resources->add($externalResource);

    return $this;
  }

  /**
   * @param ExternalResource $externalResource
   *
   * @return $this
   */
  public function removeResource(ExternalResource $externalResource): DataExternalResources
  {
    $this->resources->removeElement($externalResource);

    return $this;
  }

}
