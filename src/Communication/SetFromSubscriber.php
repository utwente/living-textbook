<?php

namespace App\Communication;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

readonly class SetFromSubscriber
{
  public function __construct(private string $from)
  {
  }

  #[AsEventListener(event: MessageEvent::class)]
  public function onMessage(MessageEvent $messageEvent): void
  {
    $email = $messageEvent->getMessage();
    if (!$email instanceof Email) {
      return;
    }

    $email->from(Address::create($this->from));
  }
}
