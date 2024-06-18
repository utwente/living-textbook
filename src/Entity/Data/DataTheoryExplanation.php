<?php

namespace App\Entity\Data;

use App\Repository\Data\DataTheoryExplanationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
#[ORM\Entity(repositoryClass: DataTheoryExplanationRepository::class)]
#[ORM\Table]
class DataTheoryExplanation implements DataInterface
{
  use BaseDataTextObject;
}
