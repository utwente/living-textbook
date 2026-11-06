<?php

namespace App\Request\Subscriber;

use App\Entity\StudyArea;
use App\Request\Wrapper\RequestStudyArea;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;

trait StudyAreaAwareSubscriberTrait
{
  protected function getStudyArea(ControllerArgumentsEvent $event, string $subject): StudyArea
  {
    $arguments = $event->getNamedArguments();
    if (!array_key_exists($subject, $arguments)) {
      throw new \InvalidArgumentException(sprintf('Subject "%s" not found', $subject));
    }

    $studyArea = $arguments[$subject];
    if ($studyArea instanceof RequestStudyArea) {
      $studyArea = $studyArea->getStudyArea();
    }

    // A study area is required
    if (!$studyArea instanceof StudyArea) {
      throw new InvalidArgumentException(sprintf('Subject "%s" does not contain the expected study area, but a "%s"',
        $subject, $studyArea === null ? 'null' : $studyArea::class));
    }

    return $studyArea;
  }
}
