<?php

namespace App\Request\Subscriber;

use App\Attribute\NotFoundWhenModernSkillsEnabled;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class NotFoundWhenModernSkillsEnabledSubscriber
{
  use StudyAreaAwareSubscriberTrait;

  public function __construct(
    private TranslatorInterface $translator,
    private RouterInterface $router,
  ) {
  }

  /** Check if a study area has modern skills enabled, and if so, throw a 404. */
  #[AsEventListener(event: KernelEvents::CONTROLLER_ARGUMENTS, priority: -50)]
  public function checkSkillConfiguration(ControllerArgumentsEvent $event): void
  {
    $attributes = $event->getAttributes(NotFoundWhenModernSkillsEnabled::class);
    if (empty($attributes)) {
      return;
    }

    $configuration = $attributes[0];

    // Check whether deprecated skills are enabled
    $studyArea = $this->getStudyArea($event, $configuration->subject);
    if ($studyArea->isDeprecatedSkills()) {
      return;
    }

    throw new NotFoundHttpException('deprecated skills are not enabled');
  }
}
