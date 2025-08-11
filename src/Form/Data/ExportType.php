<?php

namespace App\Form\Data;

use App\Export\ExportService;
use App\Export\ProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

class ExportType extends AbstractType
{

  /**
   * The default translation prefix.
   */
  public const string TRANSLATION_PREFIX = 'data.download.provider';

  /**
   * Constructs an export choice list from the {@link ExportService}'s registered {@link ProviderInterface}s.
   *
   * @param ExportService $exportService
   */
  public function __construct(private readonly ExportService $exportService)
  {
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getParent(): string
  {
    return ChoiceType::class;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'translation_prefix' => self::TRANSLATION_PREFIX,
      'choices'      => $this->exportService->getProviders(),
      'choice_label'       => static function (Options $options): callable {
        return static fn (ProviderInterface $provider, string $key) => $options['translation_prefix'] . '.' . $key;
      },
      'invalid_message' => 'export_type.invalid-type',
      'constraints'        => [
        new NotNull(message: 'export_type.not-null'),
        new Type(ProviderInterface::class, 'export_type.invalid-type')
      ],
    ]);
  }

}
