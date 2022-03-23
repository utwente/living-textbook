<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataTheoryExplanation.
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataTheoryExplanationRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataTheoryExplanation implements DataInterface
{
  use BaseDataTextObject;
}
