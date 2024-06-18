<?php

namespace App\Entity\Data;

use App\Repository\Data\DataExamplesRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: DataExamplesRepository::class)]
#[ORM\Table]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class DataExamples implements DataInterface
{
  use BaseDataTextObject;
}
