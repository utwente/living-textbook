<?php

namespace App\Form\Review\ReviewDiff;

use App\Entity\LearningPath;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ReviewLearningPathElementsDiffType extends AbstractReviewDiffType
{
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    parent::buildView($view, $form, $options);

    $originalObject = $options['original_object'];
    if ($originalObject instanceof LearningPath) {
      $view->vars['orig_elements'] = $originalObject->getElementsOrdered();
    }

    $newObject = $this->getPendingChange($options)->getObject();
    assert($newObject instanceof LearningPath);
    $view->vars['new_elements'] = $newObject->getElements();
  }
}
