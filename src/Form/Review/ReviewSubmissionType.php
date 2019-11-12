<?php

namespace App\Form\Review;

use App\Entity\Concept;
use App\Entity\LearningOutcome;
use App\Entity\LearningPath;
use App\Entity\PendingChange;
use App\Entity\Review;
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
        ->setAllowedTypes('checkboxes', 'bool')
        ->setAllowedTypes('review', 'bool');
  }

  /**
   * @param FormEvent $formEvent
   *
   * @throws EntityNotFoundException
   */
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
      // Todo: implement rest
      if ($changeType !== PendingChange::CHANGE_TYPE_EDIT) {
        continue;
      }

      $objectType = $pendingChange->getObjectType();

      $form->add(sprintf('%s__%d_h', $pendingChange->getShortObjectType(), $pendingChange->getObjectId()),
          ReviewSubmissionObjectHeaderType::class, [
              'mapped'         => false,
              'pending_change' => $pendingChange,
              'checkbox'       => $options['checkboxes'],
          ]);

      foreach ($pendingChange->getChangedFields() as $changedField) {
        $formType    = ReviewTextDiffType::class;
        $formOptions = [
            'checkbox' => $options['checkboxes'],
        ];

        // Handle different types
        if (Concept::class === $objectType) {
          if (in_array($changedField, ['relations', 'incomingRelations'])) {
            $formType                = ReviewRelationDiffType::class;
            $formOptions['incoming'] = $changedField !== 'relations';
          }

          if (in_array($changedField, ['priorKnowledge', 'learningOutcomes', 'externalResources'])) {
            $formType = ReviewSimpleListDiffType::class;
          }

          if (in_array($changedField, ['introduction', 'theoryExplanation', 'howTo', 'examples', 'selfAssessment'])) {
            $formOptions['has_data_object'] = true;
            $formOptions['ckeditor']        = true;
          }
        } else if (LearningOutcome::class === $objectType) {
          if ('text' === $changedField) {
            $formOptions['ckeditor'] = true;
          }
        } else if (LearningPath::class === $objectType) {
          if ('introduction' === $changedField) {
            $formOptions['ckeditor'] = true;
          }

          if ('elements' === $changedField) {
            $formType = ReviewLearningPathElementsDiffType::class;
          }
        }

        $form->add($this->getFieldName($pendingChange, $changedField),
            $formType, array_merge([
                'hide_label'      => true,
                'mapped'          => false,
                'original_object' => $this->reviewService->getOriginalObject($pendingChange),
                'pending_change'  => $pendingChange,
                'field'           => $changedField,
                'review'          => $options['review'],
            ], $formOptions));
      }

      $form->add(sprintf('%s__%d_f', $pendingChange->getShortObjectType(), $pendingChange->getObjectId()),
          ReviewSubmissionObjectFooterType::class, ['mapped' => false]);
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
    return sprintf('%s__%d__%s', $pendingChange->getShortObjectType(), $pendingChange->getObjectId(), $changedField);
  }
}
