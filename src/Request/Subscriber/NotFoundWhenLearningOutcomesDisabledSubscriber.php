<?php

namespace App\Request\Subscriber;

use App\Attribute\NotFoundWhenLearningOutcomesDisabled;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class NotFoundWhenLearningOutcomesDisabledSubscriber
{
  use StudyAreaAwareSubscriberTrait;

  /** Check if a study area has modern skills enabled, and if so, throw a 404. */
  #[AsEventListener(event: KernelEvents::CONTROLLER_ARGUMENTS, priority: -50)]
  public function checkSkillConfiguration(ControllerArgumentsEvent $event): void
  {
    $attributes = $event->getAttributes(NotFoundWhenLearningOutcomesDisabled::class);
    if (empty($attributes)) {
      return;
    }

    $configuration = $attributes[0];

    // Check whether unclassified skills are enabled
    $studyArea = $this->getStudyArea($event, $configuration->subject);
    if ($studyArea->isLearningOutcomesEnabled()) {
      return;
    }

    throw new NotFoundHttpException('deprecated skills are not enabled');
  }
}
