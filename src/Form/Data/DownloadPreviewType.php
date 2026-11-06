<?php

namespace App\Form\Data;

use App\Entity\StudyArea;
use App\Export\ExportService;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DownloadPreviewType extends AbstractType
{
  public function __construct(private readonly ExportService $exportService)
  {
  }

  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $view->vars['preview_data'] = $this->exportService->getPreviews($options['study_area']);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setRequired('study_area')
      ->setAllowedTypes('study_area', [StudyArea::class]);
  }
}
