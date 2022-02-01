<?php

namespace App\EntityHandler;

use App\Entity\Tag;

class TagHandler extends AbstractEntityHandler
{
  public function add(Tag $tag): void
  {
    $this->validate($tag);

    $this->em->persist($tag);
    $this->em->flush();
  }

  public function update(Tag $tag): void
  {
    $this->validate($tag);

    $this->em->flush();
  }

  public function delete(Tag $tag): void
  {
    if ($tag->isDeleted()) {
      return;
    }

    $studyArea = $tag->getStudyArea();

    // Remove tag from default if set
    if ($studyArea->getDefaultTagFilter() && $studyArea->getDefaultTagFilter()->getId() === $tag->getId()) {
      $studyArea->setDefaultTagFilter(NULL);
    }

    // Save the data
    $this->em->remove($tag);
    $this->em->flush();
  }
}
