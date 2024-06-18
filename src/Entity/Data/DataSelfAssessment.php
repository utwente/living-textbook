<?php

namespace App\Entity\Data;

use App\Repository\Data\DataSelfAssessmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
#[ORM\Entity(repositoryClass: DataSelfAssessmentRepository::class)]
#[ORM\Table]
class DataSelfAssessment implements DataInterface
{
  use BaseDataTextObject;
}
