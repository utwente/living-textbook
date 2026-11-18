<?php

namespace App\Form\Data;

use App\Data\Export\ExportService;
use App\Entity\StudyArea;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

/** @extends AbstractType<string> */
class ExportType extends AbstractType
{
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
    $resolver
      ->setRequired('study_area')
      ->setAllowedTypes('study_area', StudyArea::class)
      ->setDefaults([
        'translation_prefix' => 'data.export.provider',
        'choices'            => fn (Options $options): array => $this->exportService->getAvailableProviderKeys($options['study_area']),
        'choice_label'       => static fn (Options $options): callable => static fn (string $key): string => $options['translation_prefix'] . '.' . $key,
        'invalid_message'    => 'export-type.invalid-type',
        'constraints'        => [
          new NotNull(message: 'export-type.not-null'),
        ],
      ]);
  }
}
