<?php

namespace App\Form\Review;

use App\Entity\PendingChange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewSubmissionObjectHeaderType extends AbstractType
{
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['checkbox']      = $options['checkbox'];
    $view->vars['pendingChange'] = $options['pending_change'];
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefault('checkbox', false)
        ->setRequired('pending_change')
        ->setAllowedTypes('checkbox', 'bool')
        ->setAllowedTypes('pending_change', PendingChange::class);
  }

}
