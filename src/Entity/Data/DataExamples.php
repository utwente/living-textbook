<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class DataExamples
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataExamplesRepository")
 */
class DataExamples implements DataInterface
{
  use BaseDataTextObject;
}
