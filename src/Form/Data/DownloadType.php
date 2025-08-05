<?php

namespace App\Form\Data;

use App\Form\Type\SingleSubmitType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DownloadType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('type', ExportType::class, [
        'label' => 'data.download.type',
        'attr'  => [
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
