<?php

namespace App\Form\Review;

use App\Entity\PendingChange;
use App\Review\Exception\InvalidChangeException;
use App\Review\Model\PendingChangeObjectInfo;
use App\Review\ReviewService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisplayPendingChangeType extends AbstractType
{
  public function __construct(private readonly ReviewService $reviewService)
  {
  }

  /**
   * @throws EntityNotFoundException
   * @throws InvalidChangeException
   */
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    /** @var PendingChange $pendingChange */
    $pendingChange = $options['pending_change'];
    $field         = $options['field'];

    if (null === $pendingChange) {
      return;
    }

    $changeType               = $pendingChange->getChangeType();
    [$formType, $formOptions] = ReviewSubmissionType::getFormTypeForField($pendingChange, $field);

    $builder->add('preview', $formType, array_merge([
      'hide_label'      => true,
      'mapped'          => false,
      'original_object' => $changeType !== PendingChange::CHANGE_TYPE_ADD
          ? $this->reviewService->getOriginalObject($pendingChange)
          : null,
      'pending_change' => $pendingChange,
      'field'          => $field,
      'review'         => false,
      'show_comments'  => false,
      'show_original'  => false,
      'checkbox'       => false,
      'diff_only'      => true,
    ], $formOptions));
  }

  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    /** @var PendingChange $pendingChange */
    $pendingChange = $options['pending_change'];

    if ($pendingChange) {
      $view->vars['field']          = $options['field'];
      $view->vars['pending_change'] = $pendingChange;
      $view->vars['owner']          = $pendingChange->getOwner()->getDisplayName();
    }
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setDefault('label', false)
      ->setDefault('mapped', false)
      ->setDefault('disabled', true)
      ->setRequired('field')
      ->setRequired('pending_change_info')
      ->setAllowedTypes('pending_change_info', PendingChangeObjectInfo::class)
      ->setDefault('pending_change', null)
      ->setNormalizer('pending_change', function (Options $options) {
        /** @var PendingChangeObjectInfo $pendingChangeObjectInfo */
        $pendingChangeObjectInfo = $options->offsetGet('pending_change_info');
        $field                   = $options->offsetGet('field');

        if ($pendingChangeObjectInfo->hasChangesForField($field)) {
          return $pendingChangeObjectInfo->getPendingChangeForField($field);
        }

        return null;
      });
  }
}
