<?php

namespace App\Form\Data;

use App\Export\ExportService;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use function array_values;

class DownloadPreviewType extends AbstractType
{
  public function __construct(private readonly ExportService $exportService)
  {
  }

  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $view->vars['preview_data'] = array_values($this->exportService->getPreviews());
  }
}
