<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataSelfAssessment.
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataSelfAssessmentRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataSelfAssessment implements DataInterface
{
  use BaseDataTextObject;
}
