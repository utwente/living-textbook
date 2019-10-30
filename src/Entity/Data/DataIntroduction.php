<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataIntroduction
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataIntroductionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataIntroduction implements DataInterface
{
  use BaseDataTextObject;
}
