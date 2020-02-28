<?php

namespace App\Form\Review;

use App\Entity\PendingChange;
use App\Review\Exception\InvalidChangeException;
use App\Review\Model\PendingChangeObjectInfo;
use App\Review\ReviewService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisplayPendingChangeType extends AbstractType
{
  /**
   * @var EntityManagerInterface
   */
  private $entityManager;
  /**
   * @var ReviewService
   */
  private $reviewService;

  public function __construct(EntityManagerInterface $entityManager, ReviewService $reviewService)
  {
    $this->entityManager = $entityManager;
    $this->reviewService = $reviewService;
  }

  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   *
   * @throws EntityNotFoundException
   * @throws InvalidChangeException
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    /** @var PendingChange $pendingChange */
    $pendingChange = $options['pending_change'];
    $field         = $options['field'];

    if (NULL === $pendingChange) {
      return;
    }

    $changeType = $pendingChange->getChangeType();
    list($formType, $formOptions) = ReviewSubmissionType::getFormTypeForField($pendingChange, $field);

    $builder->add('preview', $formType, array_merge([
        'hide_label'      => true,
        'mapped'          => false,
        'original_object' => $changeType !== PendingChange::CHANGE_TYPE_ADD
            ? $this->reviewService->getOriginalObject($pendingChange)
            : NULL,
        'pending_change'  => $pendingChange,
        'field'           => $field,
        'review'          => false,
        'show_comments'   => false,
        'show_original'   => false,
        'checkbox'        => false,
        'diff_only'       => true,
    ], $formOptions));
  }

  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    /** @var PendingChange $pendingChange */
    $pendingChange = $options['pending_change'];

    if ($pendingChange) {
      $view->vars['field']          = $options['field'];
      $view->vars['pending_change'] = $pendingChange;
      $view->vars['owner']          = $pendingChange->getOwner()->getDisplayName();
    }
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefault('label', false)
        ->setDefault('mapped', false)
        ->setDefault('disabled', true)
        ->setRequired('field')
        ->setRequired('pending_change_info')
        ->setAllowedTypes('pending_change_info', PendingChangeObjectInfo::class)
        ->setDefault('pending_change', NULL)
        ->setNormalizer('pending_change', function (Options $options) {
          /** @var PendingChangeObjectInfo $pendingChangeObjectInfo */
          $pendingChangeObjectInfo = $options->offsetGet('pending_change_info');
          $field                   = $options->offsetGet('field');

          if ($pendingChangeObjectInfo->hasChangesForField($field)) {
            return $pendingChangeObjectInfo->getPendingChangeForField($field);
          }

          return NULL;
        });
  }

}
