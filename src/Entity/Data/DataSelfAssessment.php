<?php

namespace App\Entity\Data;

use App\Repository\Data\DataSelfAssessmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: DataSelfAssessmentRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class DataSelfAssessment implements DataInterface
{
  use BaseDataTextObject;
}
