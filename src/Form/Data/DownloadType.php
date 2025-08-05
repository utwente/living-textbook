<?php

namespace App\Form\Data;

use App\Export\ExportService;
use App\Form\Type\SingleSubmitType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DownloadType extends AbstractType
{
  public function __construct(private readonly ExportService $exportService)
  {
  }

  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('type', ChoiceType::class, [
        'label'         => 'data.download.type',
        'choice_loader' => $this->exportService,
        'attr'          => [
          'class' => 'download-type',
        ],
      ])
    ->add('preview', DownloadPreviewType::class)
    ->add('submit', SingleSubmitType::class, [
      'label' => 'data.download.title',
      'icon'  => 'fa-download',
      'attr'  => [
        'class' => 'btn btn-outline-success',
      ],
    ]);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'attr' => [
        'target' => '_blank',
      ],
    ]);
  }
}
