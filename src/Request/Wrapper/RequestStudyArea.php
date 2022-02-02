<?php

namespace App\Request\Wrapper;

use App\Entity\StudyArea;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class RequestStudyArea
 * A wrapper class to enable automatic study area injection into controllers,
 * without messing with the Doctrine injector
 *
 * @author BobV
 */
class RequestStudyArea
{
  public function __construct(private readonly ?StudyArea $studyArea)
  {
  }

  public function getStudyArea(): StudyArea
  {
    if ($this->studyArea === NULL) {
      throw new NotFoundHttpException("Study area is not correctly set!");
    }

    return $this->studyArea;
  }

  public function getStudyAreaId(): int
  {
    return $this->getStudyArea()->getId();
  }

  public function hasValue(): bool
  {
    return $this->studyArea !== NULL;
  }

}
