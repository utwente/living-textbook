<?php

namespace App\Entity\Data;

use App\Repository\Data\DataHowToRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: DataHowToRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class DataHowTo implements DataInterface
{
  use BaseDataTextObject;
}
