<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class DataHowTo
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataHowToRepository")
 */
class DataHowTo implements DataInterface
{
  use BaseDataTextObject;
}
