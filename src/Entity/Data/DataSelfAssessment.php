<?php

namespace App\Entity\Data;

use App\Database\Traits\Blameable;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataSelfAssessment
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataSelfAssessmentRepository")
 */
class DataSelfAssessment implements DataInterface
{
  use BaseDataTextObject;
}
