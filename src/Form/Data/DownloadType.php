<?php

namespace App\Form\Data;

use App\Export\ExportService;
use App\Form\Type\SingleSubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DownloadType extends AbstractType
{
  /** @var ExportService */
  private $exportService;

  public function __construct(ExportService $exportService)
  {
    $this->exportService = $exportService;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('type', ChoiceType::class, [
            'label'   => 'data.download.type',
            'choices' => $this->exportService->getChoices(),
            'attr'    => [
                'class' => 'download-type',
            ],
        ])
        ->add('preview', DownloadPreviewType::class)
        ->add('submit', SingleSubmitType::class, [
            'label' => 'data.download.title',
            'icon'  => 'fa-download',
            'attr'  => array(
                'class' => 'btn btn-outline-success',
            ),
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
        'attr' => [
            'target' => '_blank',
        ],
    ]);
  }


}
