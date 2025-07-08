<?php

namespace App\Form\Review\ReviewDiff;

use App\Entity\Contracts\ReviewableInterface;
use App\Entity\PendingChange;
use App\Form\Type\PrintedTextType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_key_exists;
use function assert;

class AbstractReviewDiffType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    if (!$options['review'] && !$options['show_comments']) {
      return;
    }

    if ($options['review']) {
      $builder
        ->add('comments', TextareaType::class, [
          'empty_data' => null,
          'required'   => false,
          'attr'       => [
            'placeholder' => 'review.comments-placeholder',
          ],
        ]);
    } elseif ($options['show_comments']) {
      $builder
        ->add('comments', PrintedTextType::class, [
          'text_only' => true,
        ]);
    }

    $builder->addModelTransformer(new CallbackTransformer(
      function () use ($options) {
        $pendingChange = $options['pending_change'];
        assert($pendingChange instanceof PendingChange);

        $existingComments = $pendingChange->getReviewComments() ?? [];
        $data             = null;

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
        if ($formComments === null) {
          // Only unset the field if it already existed
          // But never allow to set null in the comments field
          if (array_key_exists($field, $reviewComments)) {
            unset($reviewComments[$field]);
          }
        } else {
          $reviewComments[$field] = $formComments;
        }
        $pendingChange->setReviewComments($reviewComments);

        return null;
      }
    ));
  }

  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $pendingChange = $options['pending_change'];
    assert($pendingChange instanceof PendingChange);

    $view->vars['diff_only']         = $options['diff_only'];
    $view->vars['checkbox']          = $options['checkbox'];
    $view->vars['show_original']     = $options['show_original'];
    $view->vars['show_updated']      = $options['show_updated'];
    $view->vars['pending_change_id'] = $pendingChange->getId();
    $view->vars['change_type']       = $pendingChange->getChangeType();
    $view->vars['short_object_type'] = $pendingChange->getShortObjectType();
    $view->vars['field']             = $options['field'];
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setDefault('checkbox', false)
      ->setDefault('show_original', true)
      ->setDefault('show_updated', true)
      ->setDefault('show_comments', false)
      ->setDefault('original_object', null)
      ->setDefault('diff_only', false)
      ->setRequired([
        'pending_change',
        'field',
        'review',
      ])
      ->setAllowedTypes('checkbox', 'bool')
      ->setAllowedTypes('show_original', 'bool')
      ->setAllowedTypes('show_updated', 'bool')
      ->setAllowedTypes('show_comments', 'bool')
      ->setAllowedTypes('original_object', [ReviewableInterface::class, 'null'])
      ->setAllowedTypes('diff_only', 'bool')
      ->setAllowedTypes('pending_change', PendingChange::class)
      ->setAllowedTypes('field', 'string')
      ->setAllowedTypes('review', 'bool');
  }

  protected function getPendingChange(array $options): PendingChange
  {
    $pendingChange = $options['pending_change'];
    assert($pendingChange instanceof PendingChange);

    return $pendingChange;
  }
}
