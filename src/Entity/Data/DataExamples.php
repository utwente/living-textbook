<?php

namespace App\Entity\Data;

use App\Database\Traits\Blameable;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
