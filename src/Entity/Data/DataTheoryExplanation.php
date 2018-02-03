<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class DataTheoryExplanation
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataTheoryExplanationRepository")
 */
class DataTheoryExplanation implements DataInterface
{
  use BaseDataTextObject;
}
