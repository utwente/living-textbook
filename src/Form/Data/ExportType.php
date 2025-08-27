<?php

namespace App\Form\Data;

use App\Export\ExportService;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

/** @extends AbstractType<string> */
class ExportType extends AbstractType
{
  /** The default translation prefix. */
  public const string TRANSLATION_PREFIX = 'data.download.provider';

  public function __construct(private readonly ExportService $exportService)
  {
  }

  #[Override]
  public function getParent(): string
  {
    return ChoiceType::class;
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'translation_prefix' => self::TRANSLATION_PREFIX,
      'choices'            => $this->exportService->getAvailableProviderKeys(),
      'choice_label'       => static fn (Options $options): callable => static fn (string $key) => $options['translation_prefix'] . '.' . $key,
      'invalid_message'    => 'export_type.invalid-type',
      'constraints'        => [
        new NotNull(message: 'export_type.not-null'),
      ],
    ]);
  }
}
