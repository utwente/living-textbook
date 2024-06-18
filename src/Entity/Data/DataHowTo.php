<?php

namespace App\Entity\Data;

use App\Repository\Data\DataHowToRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
#[ORM\Entity(repositoryClass: DataHowToRepository::class)]
#[ORM\Table]
class DataHowTo implements DataInterface
{
  use BaseDataTextObject;
}
