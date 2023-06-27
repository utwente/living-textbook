<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataAdditionalResources.
 *
 * @author Erick Li
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\Data\DataAdditionalResourcesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class DataAdditionalResources implements DataInterface
{
  use BaseDataTextObject;
}
