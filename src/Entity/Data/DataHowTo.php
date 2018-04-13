<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataHowTo
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataHowToRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataHowTo implements DataInterface
{
  use BaseDataTextObject;
}
