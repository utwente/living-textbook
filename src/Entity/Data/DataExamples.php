<?php

namespace App\Entity\Data;

use App\Repository\Data\DataExamplesRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
#[ORM\Entity(repositoryClass: DataExamplesRepository::class)]
#[ORM\Table]
class DataExamples implements DataInterface
{
  use BaseDataTextObject;
}
