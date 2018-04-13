<?php

namespace App\Entity\Data;

use App\Validator\Constraint\Data\WordCount;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataIntroduction
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataIntroductionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataIntroduction implements DataInterface
{

  use BaseDataTextObject;

  /**
   * Add constraints to field from the base trait
   *
   * @param ClassMetadata $metadata
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    $metadata->addPropertyConstraints('text', [
        new Assert\NotBlank(),
        new WordCount(),
    ]);
  }
}
