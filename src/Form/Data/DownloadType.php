<?php

namespace App\Form\Data;

use App\Dto\DownloadTypeDto;
use App\Form\Type\SingleSubmitType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_combine;

/** @extends AbstractType<DownloadTypeDto> */
class DownloadType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    if ($options['url_export_enabled']) {
      $builder
        ->add('exportUrl', UrlType::class, [
          'label'    => 'study-area.export-url',
          'help'     => 'study-area.export-url-help',
          'required' => true,
          'data'     => $options['export_url'] ?? null,
        ])
        ->add('httpMethod', ChoiceType::class, [
          'label'    => 'study-area.export-url-http-method',
          'help'     => 'study-area.export-url-http-method-help',
          'choices'  => array_combine(DownloadTypeDto::METHODS, DownloadTypeDto::METHODS),
          'required' => true,
        ]);
    }

    $builder
      ->add('type', ExportType::class, [
        'label' => 'data.download.type',
        'attr'  => [
          'class' => 'download-type',
        ],
      ])
      ->add('preview', DownloadPreviewType::class, [
        'mapped' => false,
      ])
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
    $resolver
      ->setDefaults([
        'form_target'        => '_blank',
        'url_export_enabled' => false,
      ])
      ->setAllowedTypes('form_target', 'string')
      ->setAllowedTypes('url_export_enabled', 'bool');

    $resolver->setNormalizer('attr', function (Options $options, array $attr) {
      $attr['target'] = $options['form_target'] ?? '_blank';

      return $attr;
    });
  }
}
