<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataLearningOutcomes
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataLearningOutcomesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataLearningOutcomes implements DataInterface
{
  use BaseDataTextObject;
}
