<?php

namespace App\Form\Data;

use App\Export\ExportService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class DownloadPreviewType extends AbstractType
{
  private ExportService $exportService;

  public function __construct(ExportService $exportService)
  {
    $this->exportService = $exportService;
  }

  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['preview_data'] = $this->exportService->getPreviews();
  }
}
