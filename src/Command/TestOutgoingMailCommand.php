<?php

namespace App\Command;

use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function sprintf;

#[AsCommand('test:outgoing-mail')]
readonly class TestOutgoingMailCommand
{
  public function __construct(
    private MailerInterface $mailer,
    private ValidatorInterface $validator,
  ) {
  }

  public function __invoke(SymfonyStyle $io): int
  {
    $question = new Question('Recipient');
    $question->setValidator(function ($answer) {
      $violations = $this->validator->validate($answer, [
        new NotBlank(),
        new Email(),
      ]);

      if ($violations->count() !== 0) {
        throw new RuntimeException('Must be a valid email address!');
      }

      return $answer;
    });
    $email = $io->askQuestion($question);

    $this->mailer->send(
      new \Symfony\Component\Mime\Email()
        ->to($email)
        ->subject('Test email')
        ->text(sprintf('Test mail sent to %s', $email)),
    );

    return Command::SUCCESS;
  }
}
