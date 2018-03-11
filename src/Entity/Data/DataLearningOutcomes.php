<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class DataLearningOutcomes
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataLearningOutcomesRepository")
 */
class DataLearningOutcomes implements DataInterface
{
  use BaseDataTextObject;
}
