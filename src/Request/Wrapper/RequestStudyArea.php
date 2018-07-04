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

  /** @var StudyArea|null */
  private $studyArea;

  /**
   * RequestStudyArea constructor.
   *
   * @param StudyArea|null $studyArea
   */
  public function __construct(?StudyArea $studyArea)
  {
    $this->studyArea = $studyArea;
  }

  /**
   * @return StudyArea
   */
  public function getStudyArea(): StudyArea
  {
    if ($this->studyArea === NULL) {
      throw new NotFoundHttpException("Study area is not correctly set!");
    }

    return $this->studyArea;
  }

  /**
   * @return bool
   */
  public function hasValue(): bool
  {
    return $this->studyArea !== NULL;
  }

}
