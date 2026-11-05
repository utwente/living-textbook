<?php

namespace App\ExceptionHandler\Subscriber;

use App\Request\Subscriber\RequestStudyAreaSubscriber;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber
{
  #[AsEventListener(event: KernelEvents::EXCEPTION, priority: 0)]
  public function onKernelException(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();

    if ($exception instanceof AccessDeniedHttpException) {
      // Clear cached study area on 403
      $event->getRequest()->getSession()->remove(RequestStudyAreaSubscriber::STUDY_AREA_KEY);
    }
  }
}
