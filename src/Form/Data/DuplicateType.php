<?php

namespace App\Form\Data;

use App\Entity\Concept;
use App\Entity\StudyArea;
use App\Form\StudyArea\EditStudyAreaType;
use App\Form\Type\SaveType;
use App\Repository\ConceptRepository;
use App\Repository\StudyAreaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DuplicateType extends AbstractType
{
  public const CHOICE              = 'type';
  public const CHOICE_EXISTING     = 'existing';
  public const CHOICE_NEW          = 'new';
  public const EXISTING_STUDY_AREA = 'existing_study_area';
  public const NEW_STUDY_AREA      = 'new_study_area';
  public const CONCEPTS            = 'concepts';
  public const SELECT_ALL          = 'select_all';
  /** @var Security */
  private $security;
  /** @var StudyAreaRepository */
  private $studyAreaRepository;

  /** @var TranslatorInterface */
  private $translator;

  public function __construct(
      TranslatorInterface $translator, StudyAreaRepository $studyAreaRepository, Security $security)
  {
    $this->translator          = $translator;
    $this->studyAreaRepository = $studyAreaRepository;
    $this->security            = $security;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
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

              return array_filter($studyAreas, function (StudyArea $studyArea) use ($currentStudyArea) {
                return $studyArea->getId() !== $currentStudyArea->getId() && $this->security->isGranted('STUDYAREA_EDIT', $studyArea);
              });
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
            'query_builder' => function (ConceptRepository $cr) use ($currentStudyArea) {
              return $cr->createQueryBuilder('c')
                  ->where('c.studyArea = :studyArea')
                  ->setParameter('studyArea', $currentStudyArea)
                  ->orderBy('c.name', 'ASC');
            },
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

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefaults([
            'constraints' => [
                new Callback($this->checkConcepts(...), [self::CHOICE_NEW, self::CHOICE_EXISTING]),
                new Callback($this->checkNewStudyArea(...), [self::CHOICE_NEW, self::CHOICE_EXISTING]),
            ],
            'validation_groups' => function (FormInterface $form) {
              return [$form->getData()[self::CHOICE]];
            },
        ])
        ->setRequired('current_study_area')
        ->setRequired('new_study_area')
        ->setAllowedTypes('current_study_area', StudyArea::class)
        ->setAllowedTypes('new_study_area', StudyArea::class);
  }

  /**
   * Check if there is at least 1 concept selected to duplicate.
   *
   * @param $data
   */
  public function checkConcepts($data, ExecutionContextInterface $context)
  {
    if ($data['select_all'] === false && count($data['concepts']) === 0) {
      $context->buildViolation('data.concepts-no-selection')
          ->atPath('[' . self::CONCEPTS . ']')
          ->addViolation();
    }
  }

  /**
   * Check if the new study area is valid.
   *
   * @param $data
   */
  public function checkNewStudyArea($data, ExecutionContextInterface $context)
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
