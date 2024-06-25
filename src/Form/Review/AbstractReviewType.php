<?php

namespace App\Form\Review;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Repository\UserGroupRepository;
use Doctrine\ORM\NonUniqueResultException;
use Override;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class AbstractReviewType extends AbstractType
{
  public function __construct(
    private readonly UserGroupRepository $userGroupRepository,
    private readonly Security $security)
  {
  }

  protected function addNotes(FormBuilderInterface $builder): AbstractReviewType
  {
    $builder
      ->add('notes', TextareaType::class, [
        'required' => false,
        'label'    => 'review.notes',
        'help'     => 'review.notes-help',
      ]);

    return $this;
  }

  /** @throws NonUniqueResultException */
  protected function addReviewer(FormBuilderInterface $builder, array $options): AbstractReviewType
  {
    $self = $this->security->getUser();
    assert($self instanceof User);
    $studyArea = $options['study_area'];
    assert($studyArea instanceof StudyArea);
    $userGroup      = $this->userGroupRepository->getForType($studyArea, UserGroup::GROUP_REVIEWER);
    $groupReviewers = array_filter($userGroup ? $userGroup->getUsers()->toArray() : [],
      fn (User $user) => $user->getId() != $self->getId());

    $possibleUsers = array_merge(
      [$studyArea->getOwner()], // Owner is always available for review
      $groupReviewers
    );

    $builder
      ->add('requestedReviewBy', ChoiceType::class, [
        'required'     => true,
        'label'        => 'review.reviewer',
        'select2'      => true,
        'choice_label' => 'selectionName',
        'choices'      => $possibleUsers,
        'help'         => 'review.reviewer-help',
        'constraints'  => [
          new NotNull(),
        ],
      ]);

    return $this;
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setRequired('study_area')
      ->setAllowedTypes('study_area', [StudyArea::class]);
  }
}
