<?php

namespace App\Communication;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SetFromSubscriber implements EventSubscriberInterface
{
  private string $from;

  public function __construct(string $from)
  {
    $this->from = $from;
  }

  public static function getSubscribedEvents()
  {
    return [
        MessageEvent::class => 'onMessage',
    ];
  }

  public function onMessage(MessageEvent $messageEvent)
  {
    $email = $messageEvent->getMessage();
    if (!$email instanceof Email) {
      return;
    }

    $email->from(Address::create($this->from));
  }
}
