<?php

namespace App\Form\StudyArea;

use App\Entity\StudyArea;
use App\Entity\StudyAreaGroup;
use App\Entity\Tag;
use App\Form\Type\CkEditorType;
use App\Form\Type\SaveType;
use App\Repository\StudyAreaGroupRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditStudyAreaType extends AbstractType
{
  public function __construct(
    private readonly Security $security,
    private readonly EntityManagerInterface $em,
  ) {
  }

  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $studyArea = $options['studyArea'];
    assert($studyArea instanceof StudyArea);
    $editing = $studyArea->getId() !== null;
    $builder
      ->add('name', TextType::class, [
        'label'      => 'study-area.name',
        'empty_data' => '',
      ])
      ->add('accessType', ChoiceType::class, [
        'label'                     => 'study-area.access-type',
        'help'                      => 'study-area.access-type-change-note',
        'choices'                   => $studyArea->getAvailableAccessTypes($this->security, $this->em),
        'choice_label'              => fn ($value) => ucfirst((string)$value),
        'choice_translation_domain' => false,
        'select2'                   => true,
      ]);

    if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
      $builder
        ->add('group', EntityType::class, [
          'required'      => false,
          'class'         => StudyAreaGroup::class,
          'label'         => 'study-area.groups.group',
          'choice_label'  => 'name',
          'select2'       => true,
          'query_builder' => fn (StudyAreaGroupRepository $repo) => $repo->createQueryBuilder('sag')
            ->orderBy('sag.name', 'ASC'),
        ]);
    }

    $builder
      ->add('description', CkEditorType::class, [
        'label'     => 'study-area.description',
        'required'  => false,
        'studyArea' => $studyArea,
      ])
      ->add('printHeader', TextType::class, [
        'label'    => 'study-area.print-header',
        'help'     => 'study-area.print-header-help',
        'required' => false,
      ])
      ->add('printIntroduction', TextareaType::class, [
        'label'    => 'study-area.print-introduction',
        'help'     => 'study-area.print-introduction-help',
        'required' => false,
      ]);

    if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
      $builder
        ->add('openAccess', CheckboxType::class, [
          'required' => false,
          'label'    => 'study-area.open-access',
          'help'     => 'study-area.open-access-help',
        ])
        ->add('trackUsers', CheckboxType::class, [
          'label'    => 'study-area.track-users',
          'help'     => 'study-area.track-users-help',
          'required' => false,
        ])
        ->add('analyticsDashboardEnabled', CheckboxType::class, [
          'label'    => 'study-area.analytics-dashboard',
          'help'     => 'study-area.analytics-dashboard-help',
          'required' => false,
        ]);
    }

    $builder
      ->add('reviewModeEnabled', CheckboxType::class, [
        'label'    => 'study-area.review-mode',
        'help'     => 'study-area.review-mode-help',
        'required' => false,
      ])
      ->add('apiEnabled', CheckboxType::class, [
        'label'    => 'study-area.api-enabled',
        'help'     => 'study-area.api-enabled-help',
        'required' => false,
      ])
      ->add('dotron', CheckboxType::class, [
        'required' => false,
        'label'    => 'study-area.dotron',
        'help'     => 'study-area.dotron-help',
      ]);

    if ($studyArea->getId()) {
      $builder
        ->add('defaultTagFilter', EntityType::class, [
          'required'      => false,
          'label'         => 'study-area.default-tag-filter',
          'help'          => 'study-area.default-tag-filter-help',
          'class'         => Tag::class,
          'choice_label'  => 'name',
          'select2'       => true,
          'query_builder' => fn (TagRepository $tagRepository) => $tagRepository->findForStudyAreaQb($studyArea),
        ]);
    }

    if (!$options['hide_submit']) {
      $builder
        ->add('submit', SaveType::class, [
          'enable_save_and_list' => !$options['save_only'] && $options['save_and_list'],
          'save_and_list_label'  => 'form.save-and-dashboard',
          'enable_cancel'        => !$options['save_only'],
          'cancel_label'         => 'form.discard',
          'cancel_route'         => $editing ? $options['cancel_route_edit'] : $options['cancel_route'],
          'cancel_route_params'  => [],
        ]);
    }
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setDefaults([
        'data_class'        => StudyArea::class,
        'save_only'         => false,
        'save_and_list'     => true,
        'cancel_route'      => 'app_studyarea_list',
        'cancel_route_edit' => 'app_default_dashboard',
        'hide_submit'       => false,
      ])
      ->setAllowedTypes('save_only', 'bool')
      ->setAllowedTypes('save_and_list', 'bool')
      ->setRequired('studyArea')
      ->setAllowedTypes('studyArea', StudyArea::class)
      ->setAllowedTypes('cancel_route', 'string')
      ->setAllowedTypes('cancel_route_edit', 'string')
      ->setRequired('select_owner')
      ->setAllowedTypes('select_owner', 'bool')
      ->setAllowedTypes('hide_submit', 'bool');
  }
}
