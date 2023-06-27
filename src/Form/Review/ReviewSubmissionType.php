<?php

namespace App\Form\Review;

use App\Entity\Concept;
use App\Entity\LearningOutcome;
use App\Entity\LearningPath;
use App\Entity\PendingChange;
use App\Entity\Review;
use App\Form\Review\ReviewDiff\ReviewCheckboxDiffType;
use App\Form\Review\ReviewDiff\ReviewLearningPathElementsDiffType;
use App\Form\Review\ReviewDiff\ReviewRelationDiffType;
use App\Form\Review\ReviewDiff\ReviewSimpleListDiffType;
use App\Form\Review\ReviewDiff\ReviewTextDiffType;
use App\Form\Type\SaveType;
use App\Review\ReviewService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use InvalidArgumentException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewSubmissionType extends AbstractType
{
  /** @var EntityManagerInterface */
  private $entityManager;
  /** @var ReviewService */
  private $reviewService;

  public function __construct(EntityManagerInterface $entityManager, ReviewService $reviewService)
  {
    $this->entityManager = $entityManager;
    $this->reviewService = $reviewService;
  }

  public static function getFormTypeForField(PendingChange $pendingChange, string $field)
  {
    $formType    = ReviewTextDiffType::class;
    $formOptions = [];

    // Handle different types
    switch ($pendingChange->getObjectType()) {
      case Concept::class:
        if (in_array($field, ['instance'])) {
          $formType = ReviewCheckboxDiffType::class;
        } elseif (in_array($field, ['relations', 'incomingRelations'])) {
          $formType                = ReviewRelationDiffType::class;
          $formOptions['incoming'] = $field !== 'relations';
        } elseif (in_array($field, ['priorKnowledge', 'learningOutcomes', 'externalResources', 'contributors'])) {
          $formType = ReviewSimpleListDiffType::class;
        } elseif (in_array($field, ['introduction', 'theoryExplanation', 'howTo', 'examples', 'selfAssessment', 'additionalResources'])) {
          $formOptions['has_data_object'] = true;
          $formOptions['ckeditor']        = true;
        }

        break;
      case LearningOutcome::class:
        if ('text' === $field) {
          $formOptions['ckeditor'] = true;
        }
        break;
      case LearningPath::class:
        if ('introduction' === $field) {
          $formOptions['ckeditor'] = true;
        } elseif ('elements' === $field) {
          $formType = ReviewLearningPathElementsDiffType::class;
        }
        break;
    }

    return [$formType, $formOptions];
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {
      $this->buildFields($formEvent);
    });
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefault('checkboxes', false)
        ->setDefault('data_class', Review::class)
        ->setDefault('review', true)
        ->setDefault('show_comments', false)
        ->setAllowedTypes('checkboxes', 'bool')
        ->setAllowedTypes('review', 'bool')
        ->setAllowedTypes('show_comments', 'bool');
  }

  /** @throws EntityNotFoundException */
  private function buildFields(FormEvent $formEvent): void
  {
    $form    = $formEvent->getForm();
    $options = $form->getConfig()->getOptions();
    $review  = $formEvent->getData();
    if (!$review || !$review instanceof Review) {
      throw new InvalidArgumentException(sprintf('Only the "%s" data type is supported!', Review::class));
    }

    // Add a field per pending change, per changed field. The actual field added depends on the type of change
    foreach ($review->getPendingChanges() as $pendingChange) {
      $changeType = $pendingChange->getChangeType();

      $form->add(sprintf('%s__%d_h', $pendingChange->getShortObjectType(), $pendingChange->getId()),
          ReviewSubmissionObjectHeaderType::class, [
              'mapped'           => false,
              'pending_change'   => $pendingChange,
              'checkbox'         => $options['checkboxes'],
              'full_change_only' => $changeType !== PendingChange::CHANGE_TYPE_EDIT,
          ]);

      foreach ($pendingChange->getChangedFields() as $changedField) {
        [$formType, $formOptions] = self::getFormTypeForField($pendingChange, $changedField);

        $form->add($this->getFieldName($pendingChange, $changedField),
            $formType, array_merge([
                'hide_label'      => true,
                'mapped'          => false,
                'original_object' => $changeType !== PendingChange::CHANGE_TYPE_ADD
                    ? $this->reviewService->getOriginalObject($pendingChange)
                    : null,
                'pending_change' => $pendingChange,
                'field'          => $changedField,
                'review'         => $options['review'],
                'show_comments'  => $options['show_comments'],
                'show_original'  => $changeType !== PendingChange::CHANGE_TYPE_ADD,
                'checkbox'       => $changeType === PendingChange::CHANGE_TYPE_EDIT && $options['checkboxes'],
            ], $formOptions));
      }

      $form->add(sprintf('%s__%d_f', $pendingChange->getShortObjectType(), $pendingChange->getId()),
          ReviewSubmissionObjectFooterType::class, [
              'mapped'         => false,
              'pending_change' => $pendingChange,
          ]);
    }

    if ($options['review']) {
      $form
          ->add('submit', SaveType::class, [
              'enable_cancel'        => true,
              'cancel_route'         => 'app_review_submissions',
              'enable_save_and_list' => false,
          ]);
    }
  }

  private function getFieldName(PendingChange $pendingChange, string $changedField): string
  {
    return sprintf('%s__%d__%d__%s', strtolower($pendingChange->getShortObjectType()), $pendingChange->getId(), $pendingChange->getObjectId(), $changedField);
  }
}
