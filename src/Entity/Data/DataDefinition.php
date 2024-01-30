<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataDefinition.
 *
 * @author Rohuru
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataDefinitionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataDefinition implements DataInterface
{
  use BaseDataTextObject;
}
