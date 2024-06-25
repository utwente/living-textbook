<?php

namespace App\Entity\Data;

use App\Repository\Data\DataIntroductionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: DataIntroductionRepository::class)]
#[ORM\Table]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class DataIntroduction implements DataInterface
{
  use BaseDataTextObject;
}
