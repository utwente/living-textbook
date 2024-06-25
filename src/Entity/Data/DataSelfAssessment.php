<?php

namespace App\Entity\Data;

use App\Repository\Data\DataSelfAssessmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: DataSelfAssessmentRepository::class)]
#[ORM\Table]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class DataSelfAssessment implements DataInterface
{
  use BaseDataTextObject;
}
