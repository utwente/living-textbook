<?php

namespace App\Form\Data;

use App\Entity\Concept;
use App\Entity\StudyArea;
use App\Form\StudyArea\EditStudyAreaType;
use App\Form\Type\SaveType;
use App\Repository\ConceptRepository;
use App\Repository\StudyAreaRepository;
use Override;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DuplicateType extends AbstractType
{
  final public const string CHOICE              = 'type';
  final public const string CHOICE_EXISTING     = 'existing';
  final public const string CHOICE_NEW          = 'new';
  final public const string EXISTING_STUDY_AREA = 'existing_study_area';
  final public const string NEW_STUDY_AREA      = 'new_study_area';
  final public const string CONCEPTS            = 'concepts';
  final public const string SELECT_ALL          = 'select_all';
  public function __construct(
    private readonly TranslatorInterface $translator,
    private readonly StudyAreaRepository $studyAreaRepository,
    private readonly Security $security)
  {
  }

  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $defaultGroupName = $this->translator->trans('study-area.groups.default-name');

    /** @var StudyArea $currentStudyArea */
    $currentStudyArea = $options['current_study_area'];

    $builder
      ->add(self::CHOICE, ChoiceType::class, [
        'choices' => [
          'study-area.new'      => self::CHOICE_NEW,
          'study-area.existing' => self::CHOICE_EXISTING,
        ],
        'data'     => self::CHOICE_NEW,
        'expanded' => true,
      ])
      ->add(self::EXISTING_STUDY_AREA, ChoiceType::class, [
        'required'    => true,
        'form_header' => 'study-area.existing',
        'placeholder' => 'dashboard.select-one',
        'select2'     => true,
        'group_by'    => function (StudyArea $studyArea) use ($defaultGroupName) {
          if (!$studyArea->getGroup()) {
            return $defaultGroupName;
          }

          return $studyArea->getGroup()->getName();
        },
        'choice_loader' => new CallbackChoiceLoader(function () use ($currentStudyArea) {
          $studyAreas = $this->studyAreaRepository->findBy(['reviewModeEnabled' => false, 'frozenOn' => null]);

          return array_filter($studyAreas, fn (StudyArea $studyArea) => $studyArea->getId() !== $currentStudyArea->getId() && $this->security->isGranted('STUDYAREA_EDIT', $studyArea));
        }),
        'choice_label' => 'name',
        'constraints'  => [
          new NotNull(['groups' => [self::CHOICE_EXISTING]]),
        ],
      ])
      ->add(self::NEW_STUDY_AREA, EditStudyAreaType::class, [
        'studyArea'    => $options['new_study_area'],
        'select_owner' => false,
        'form_header'  => 'study-area.new',
        'hide_label'   => true,
        'hide_submit'  => true,
      ])
      ->add(self::CONCEPTS, EntityType::class, [
        'form_header'   => 'data.concepts-to-duplicate',
        'label'         => 'data.concepts',
        'required'      => false,
        'select2'       => true,
        'multiple'      => true,
        'class'         => Concept::class,
        'choice_label'  => 'name',
        'query_builder' => fn (ConceptRepository $cr) => $cr->createQueryBuilder('c')
          ->where('c.studyArea = :studyArea')
          ->setParameter('studyArea', $currentStudyArea)
          ->orderBy('c.name', 'ASC'),
      ])
      ->add(self::SELECT_ALL, CheckboxType::class, [
        'label'    => 'data.select-all',
        'required' => false,
        'help'     => 'data.select-all-info',
      ])
      ->add('submit', SaveType::class, [
        'enable_save'          => true,
        'save_label'           => 'data.duplicate',
        'save_icon'            => 'fa-copy',
        'enable_save_and_list' => false,
        'enable_cancel'        => true,
        'cancel_route'         => 'app_concept_list',
        'cancel_route_params'  => [],
      ]);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setDefaults([
        'constraints' => [
          new Callback($this->checkConcepts(...), [self::CHOICE_NEW, self::CHOICE_EXISTING]),
          new Callback($this->checkNewStudyArea(...), [self::CHOICE_NEW, self::CHOICE_EXISTING]),
        ],
        'validation_groups' => fn (FormInterface $form) => [$form->getData()[self::CHOICE]],
      ])
      ->setRequired('current_study_area')
      ->setRequired('new_study_area')
      ->setAllowedTypes('current_study_area', StudyArea::class)
      ->setAllowedTypes('new_study_area', StudyArea::class);
  }

  /** Check if there is at least 1 concept selected to duplicate. */
  public function checkConcepts($data, ExecutionContextInterface $context): void
  {
    if ($data['select_all'] === false && (is_countable($data['concepts']) ? count($data['concepts']) : 0) === 0) {
      $context->buildViolation('data.concepts-no-selection')
        ->atPath('[' . self::CONCEPTS . ']')
        ->addViolation();
    }
  }

  /** Check if the new study area is valid. */
  public function checkNewStudyArea($data, ExecutionContextInterface $context): void
  {
    if ($context->getGroup() === self::CHOICE_NEW) {
      $context
        ->getValidator()
        ->inContext($context)
        ->atPath('[' . self::NEW_STUDY_AREA . ']')
        ->validate($data[self::NEW_STUDY_AREA], null, ['Default']);
    }
  }
}
