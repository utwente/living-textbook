<?php

namespace App\Entity\Data;

use App\Repository\Data\DataTheoryExplanationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: DataTheoryExplanationRepository::class)]
#[ORM\Table]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class DataTheoryExplanation implements DataInterface
{
  use BaseDataTextObject;
}
