<?php

namespace App\Form\Review\ReviewDiff;

use App\Entity\Contracts\ReviewableInterface;
use App\Entity\PendingChange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ReviewTextDiffType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    if ($options['review']) {
      $builder
          ->add('comments', TextareaType::class, [
              'empty_data' => NULL,
              'required'   => false,
              'attr'       => [
                  'placeholder' => 'review.comments-placeholder',
              ],
          ]);
    }

    $builder->addModelTransformer(new CallbackTransformer(
        function () use ($options) {
          $pendingChange = $options['pending_change'];
          assert($pendingChange instanceof PendingChange);

          $existingComments = $pendingChange->getReviewComments() ?? [];
          $data             = NULL;

          if (array_key_exists($options['field'], $existingComments)) {
            $data = $existingComments[$options['field']];
          }

          return [
              'comments' => $data,
          ];
        },
        function (array $formData) use ($options) {
          $pendingChange = $options['pending_change'];
          $field         = $options['field'];
          assert($pendingChange instanceof PendingChange);

          $reviewComments = $pendingChange->getReviewComments() ?? [];
          $formComments   = $formData['comments'];
          if ($formComments === NULL) {
            // Only unset the field if it already existed
            // But never allow to set null in the comments field
            if (array_key_exists($field, $reviewComments)) {
              unset($reviewComments[$field]);
            }
          } else {
            $reviewComments[$field] = $formComments;
          }
          $pendingChange->setReviewComments($reviewComments);

          return NULL;
        }
    ));
  }

  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $propertyAccessor = PropertyAccess::createPropertyAccessor();

    $originalObject = $options['original_object'];
    $field          = $options['field'];
    $pendingChange  = $options['pending_change'];
    assert($pendingChange instanceof PendingChange);

    $view->vars['short_object_type'] = $pendingChange->getShortObjectType();
    $view->vars['orig_text']         = $propertyAccessor->getValue($originalObject, $field);
    $view->vars['new_text']          = $propertyAccessor->getValue($pendingChange->getObject(), $field);
    $view->vars['field']             = $options['field'];
    $view->vars['ckeditor']          = $options['ckeditor'];

    if ($options['has_data_object']) {
      $view->vars['orig_text'] = $view->vars['orig_text']->getText();
      $view->vars['new_text']  = $view->vars['new_text']->getText();
    }
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired([
            'original_object',
            'pending_change',
            'field',
            'review',
        ])
        ->setDefault('has_data_object', false)
        ->setDefault('ckeditor', false)
        ->setAllowedTypes('original_object', ReviewableInterface::class)
        ->setAllowedTypes('pending_change', PendingChange::class)
        ->setAllowedTypes('field', 'string')
        ->setAllowedTypes('review', 'bool')
        ->setAllowedTypes('has_data_object', 'bool')
        ->setAllowedTypes('ckeditor', 'bool');
  }
}
