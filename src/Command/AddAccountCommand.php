<?php

namespace App\Command;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Override;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

use function assert;
use function sprintf;

#[AsCommand('ltb:add:account')]
class AddAccountCommand extends Command
{
  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly UserRepository $userRepository,
    private readonly ValidatorInterface $validator,
    private readonly UserPasswordHasherInterface $passwordHasher,
  ) {
    parent::__construct();
  }

  #[Override]
  protected function configure(): void
  {
    $this
      ->setDescription('Directly add a new local user to the database')
      ->addOption('with-area', mode: InputOption::VALUE_NONE, description: 'Directly add a area owner by the added user');
  }

  #[Override]
  public function run(InputInterface $input, OutputInterface $output): int
  {
    $style  = new SymfonyStyle($input, $output);
    $helper = $this->getHelper('question');
    assert($helper instanceof QuestionHelper);

    $this->entityManager->beginTransaction();

    try {
      ($user = new User())
        ->setGivenName($helper->ask($input, $output,
          $this->buildQuestion('Provide a given name', 'LTB')
            ->setValidator(fn ($answer) => $this->validateProp('givenName', $answer))
        ))
        ->setFamilyName($helper->ask($input, $output,
          $this->buildQuestion('Provide a family name', 'Developer')
            ->setValidator(fn ($answer) => $this->validateProp('familyName', $answer))
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
                new Length(['max' => 72]),
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

      if (!$this->validateObject($user, $style)) {
        return Command::FAILURE;
      }

      $this->entityManager->persist($user);
      $this->entityManager->flush();

      $style->success(sprintf('User %s has been created successfully!', $user->getDisplayName()));

      if ($input->getOption('with-area')) {
        ($area = new StudyArea())
          ->setOwner($user)
          ->setName($helper->ask($input, $output,
            $this->buildQuestion('Provide the new study area name', 'Developer Area')
          ));

        if (!$this->validateObject($area, $style)) {
          return Command::FAILURE;
        }

        $this->entityManager->persist($area);
        $this->entityManager->flush();

        $style->success(sprintf('Area %s has been created successfully!', $area->getName()));
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
