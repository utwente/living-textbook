<?php

namespace App\Entity\Data;

use App\Database\Traits\Blameable;
use App\Database\Traits\SoftDeletable;
use App\Validator\Constraint\Data\WordCount;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class DataIntroduction
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataIntroductionRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataIntroduction implements DataInterface
{

  use Blameable;
  use SoftDeletable;

  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * Introduction and definition
   *
   * @var string
   *
   * @ORM\Column(name="introduction", type="text", nullable=false)
   * @Assert\NotBlank()
   * @WordCount()
   */
  private $introduction;

  /**
   * DataIntroduction constructor.
   */
  public function __construct()
  {
    $this->introduction = '';
  }

  /**
   * @return bool
   */
  public function hasData(): bool
  {
    return $this->introduction != '';
  }

  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getIntroduction(): string
  {
    return $this->introduction;
  }

  /**
   * @param string $introduction
   *
   * @return DataIntroduction
   */
  public function setIntroduction(string $introduction): DataIntroduction
  {
    $this->introduction = $introduction;

    return $this;
  }

}
