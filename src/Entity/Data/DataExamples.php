<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataExamples.
 *
 * @author BobV
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataExamplesRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataExamples implements DataInterface
{
  use BaseDataTextObject;
}
