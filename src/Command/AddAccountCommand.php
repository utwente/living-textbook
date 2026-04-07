<?php

namespace App\Command;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

use function sprintf;

#[AsCommand('ltb:add:account', description: 'Directly add a new local user to the database')]
final readonly class AddAccountCommand
{
  public function __construct(
    private EntityManagerInterface $entityManager,
    private UserRepository $userRepository,
    private ValidatorInterface $validator,
    private UserPasswordHasherInterface $passwordHasher,
  ) {
  }

  public function __invoke(
    SymfonyStyle $io,
    InputInterface $input,
    OutputInterface $output,
    #[Option(description: 'Directly add a area owned by the added user')]
    bool $withArea = false,
  ): int {
    $helper = new QuestionHelper();

    $this->entityManager->beginTransaction();

    try {
      ($user = new User())
        ->setGivenName($helper->ask($input, $output,
          $this->buildQuestion('Provide a given name', 'LTB')
            ->setValidator(fn ($answer): mixed => $this->validateProp('givenName', $answer))
        ))
        ->setFamilyName($helper->ask($input, $output,
          $this->buildQuestion('Provide a family name', 'Developer')
            ->setValidator(fn ($answer): mixed => $this->validateProp('familyName', $answer))
        ))
        ->setUsername($helper->ask($input, $output,
          $this->buildQuestion('Provide a username (email)', 'developer@ltb.local')
            ->setValidator(function ($answer) {
              $this->validateProp('username', $answer);
              if ($this->userRepository->findOneBy(['username' => $answer])) {
                throw new Exception('This e-mail address is already in user');
              }

              return $answer;
            })
        ))
        ->setPassword($this->passwordHasher->hashPassword($user, $helper->ask($input, $output,
          new Question('Provide a password: ')
            ->setHidden(true)
            ->setValidator(function ($answer) {
              $violations = $this->validator->validate($answer, [
                new NotBlank(),
                new Length(max: 72),
                new PasswordStrength(minStrength: 4, minLength: 8, message: 'user.password-too-weak'),
              ]);

              if ($violations->count() === 0) {
                return $answer;
              }

              throw new Exception($violations->get(0)->getMessage());
            })
        )))
        ->setIsAdmin($helper->ask($input, $output,
          new ConfirmationQuestion('Must this user be marked as admin [y/N]? ', false)
        ))
        ->setDisplayName($user->getGivenName() . ' ' . $user->getFamilyName())
        ->setFullName($user->getDisplayName());

      if (!$this->validateObject($user, $io)) {
        return Command::FAILURE;
      }

      $this->entityManager->persist($user);
      $this->entityManager->flush();

      $io->success(sprintf('User %s has been created successfully!', $user->getDisplayName()));

      if ($withArea) {
        ($area = new StudyArea())
          ->setOwner($user)
          ->setName($helper->ask($input, $output,
            $this->buildQuestion('Provide the new study area name', 'Developer Area')
          ));

        if (!$this->validateObject($area, $io)) {
          return Command::FAILURE;
        }

        $this->entityManager->persist($area);
        $this->entityManager->flush();

        $io->success(sprintf('Area %s has been created successfully!', $area->getName()));
      }

      $this->entityManager->commit();
    } catch (Throwable $e) {
      $this->entityManager->rollback();

      throw $e;
    }

    return Command::SUCCESS;
  }

  private function buildQuestion(string $question, string $default): Question
  {
    return new Question(sprintf('%s [%s]: ', $question, $default), $default);
  }

  private function validateProp(string $property, mixed $value): mixed
  {
    $violations = $this->validator->validatePropertyValue(User::class, $property, $value);
    if ($violations->count() === 0) {
      return $value;
    }

    throw new Exception($violations->get(0)->getMessage());
  }

  private function validateObject(mixed $object, SymfonyStyle $style): bool
  {
    $violations = $this->validator->validate($object);
    if ($violations->count() === 0) {
      return true;
    }

    $style->error($violations->get(0)->getMessage());

    return false;
  }
}
