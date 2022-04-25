<?php

namespace App\EntityHandler;

use App\Entity\StudyArea;

class StudyAreaEntityHandler extends AbstractEntityHandler
{
  public function update(StudyArea $studyArea): void
  {
    $this->validate($studyArea);

    $this->em->flush();
  }
}
