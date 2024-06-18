<?php

namespace App\Entity\Data;

use App\Repository\Data\DataIntroductionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
#[ORM\Entity(repositoryClass: DataIntroductionRepository::class)]
#[ORM\Table]
class DataIntroduction implements DataInterface
{
  use BaseDataTextObject;
}
