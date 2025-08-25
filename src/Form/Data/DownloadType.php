<?php

namespace App\Form\Data;

use App\Form\Type\SingleSubmitType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
          'label'   => 'study-area.export-url-http-method',
          'help'    => 'study-area.export-url-http-method-help',
          'choices' => [
            'POST' => 'POST',
            'PUT'  => 'PUT',
          ],
          'data'     => 'PUT', // Default to PUT,
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
      'form_target'        => '_blank',
      'export_url'         => null,
      'url_export_enabled' => false,
    ])
    ->setAllowedTypes('form_target', 'string')
    ->setAllowedTypes('export_url', ['null', 'string'])
    ->setAllowedTypes('url_export_enabled', 'bool');

    $resolver->setNormalizer('attr', function (Options $options, $attr) {
      $attr['target'] = $options['form_target'] ?? '_blank';

      return $attr;
    });

    $resolver->setNormalizer('data', function (Options $options, $data) {
      $data['export_url']         = $options['export_url'] ?? null;
      $data['url_export_enabled'] = $options['url_export_enabled'] ?? false;

      return $data;
    });
  }
}
