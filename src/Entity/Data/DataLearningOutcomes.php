<?php

namespace App\Entity\Data;

use App\Database\Traits\Blameable;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataLearningOutcomes
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataLearningOutcomes")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataLearningOutcomes implements DataInterface
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
   * Learning outcomes
   *
   * @var string|null
   *
   * @ORM\Column(name="learning_outcomes", type="text", nullable=true)
   */
  private $learningOutcomes;

  /**
   * Determine whether this block has data
   *
   * @return bool
   */
  function hasData(): bool
  {
    return $this->learningOutcomes !== null && $this->learningOutcomes != '';
  }

  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * @return string|null
   */
  public function getLearningOutcomes(): ?string
  {
    return $this->learningOutcomes;
  }

  /**
   * @param string|null $learningOutcomes
   *
   * @return DataLearningOutcomes
   */
  public function setLearningOutcomes(?string $learningOutcomes): DataLearningOutcomes
  {
    $this->learningOutcomes = $learningOutcomes;

    return $this;
  }
}
