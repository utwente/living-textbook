<?php

namespace App\Form\Review;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Repository\UserGroupRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotNull;

class AbstractReviewType extends AbstractType
{
  /**
   * @var Security
   */
  private $security;
  /**
   * @var UserGroupRepository
   */
  private $userGroupRepository;

  /**
   * AbstractReviewType constructor.
   *
   * @param UserGroupRepository $userGroupRepository
   * @param Security            $security
   */
  public function __construct(UserGroupRepository $userGroupRepository, Security $security)
  {
    $this->userGroupRepository = $userGroupRepository;
    $this->security            = $security;
  }

  /**
   * @param FormBuilderInterface $builder
   *
   * @return AbstractReviewType
   */
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

  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   *
   * @return AbstractReviewType
   * @throws NonUniqueResultException
   */
  protected function addReviewer(FormBuilderInterface $builder, array $options): AbstractReviewType
  {
    $self = $this->security->getUser();
    assert($self instanceof User);
    $studyArea = $options['study_area'];
    assert($studyArea instanceof StudyArea);
    $userGroup      = $this->userGroupRepository->getForType($studyArea, UserGroup::GROUP_REVIEWER);
    $groupReviewers = array_filter($userGroup ? $userGroup->getUsers()->toArray() : [],
        function (User $user) use ($self) {
          return $user->getId() != $self->getId();
        });

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

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('study_area')
        ->setAllowedTypes('study_area', [StudyArea::class]);
  }

}
