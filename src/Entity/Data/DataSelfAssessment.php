<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;

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
