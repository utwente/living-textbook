<?php

namespace App\EntityHandler;

use App\Entity\ConceptRelation;

class ConceptRelationEntityHandler extends AbstractEntityHandler
{
  public function delete(ConceptRelation $conceptRelation): void
  {
    $this->em->remove($conceptRelation);
    $this->em->flush();
  }
}
