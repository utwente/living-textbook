<?php

namespace App\Form\Type;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

use function array_filter;
use function array_map;
use function implode;
use function mb_strtolower;
use function preg_split;
use function trim;

use const PHP_EOL;

class EmailListType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->addModelTransformer(new CallbackTransformer(
        function (?array $transform) {
          if ($transform === null) {
            return '';
          }

          return implode(PHP_EOL, $transform);
        },
        function (?string $reverseTransform) {
          if ($reverseTransform === null) {
            return [];
          }

          $emails = array_map(fn ($email) => mb_strtolower(trim($email)), preg_split('/\r\n|\r|\n|,/', $reverseTransform));

          return array_filter($emails, fn ($email) => $email !== '');
        }
      ));
  }

  #[Override]
  public function getParent(): ?string
  {
    return TextareaType::class;
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'required'    => true,
      'label'       => 'permissions.emails',
      'help'        => 'permissions.emails-help',
      'constraints' => [
        'constraints' => new All([
          new NotBlank(),
          new Email(['message' => 'emails.invalid-email']),
        ]),
      ],
      'attr' => [
        'rows' => 10,
      ],
    ]);
  }
}
