<?php

namespace App\Form\Data;

use App\Data\Import\ImportService;
use App\Dto\ExportTypeDto;
use App\Dto\ImportTypeDto;
use App\Entity\StudyArea;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;

/** @extends AbstractType<ImportTypeDto> */
class ImportType extends AbstractType
{
  public function __construct(private readonly ImportService $importService)
  {
  }

  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('type', ChoiceType::class, [
        'choices'         => $this->importService->getAvailableProviderKeys($options['study_area']),
        'choice_label'    => static fn (string $key): string => 'data.import.provider.' . $key,
        'invalid_message' => 'export-type.invalid-type',
        'constraints'     => [
          new NotNull(message: 'export-type.not-null'),
        ],
      ])
      ->add('json', FileType::class, [
        'label'       => 'data.import.json-file',
        'constraints' => [
          new NotNull(),
          new File(mimeTypes: ['application/json', 'text/plain', 'text/html']), // Allow text/html as the imported file can be detected wrongly
        ],
      ])
      ->add('submit', SubmitType::class);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setRequired('study_area')
      ->setAllowedTypes('study_area', StudyArea::class);
  }
}
