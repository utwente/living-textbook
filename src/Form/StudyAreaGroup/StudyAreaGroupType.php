<?php

namespace App\Form\StudyAreaGroup;

use App\Entity\StudyArea;
use App\Entity\StudyAreaGroup;
use App\Form\Type\SaveType;
use App\Repository\StudyAreaRepository;
use Override;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudyAreaGroupType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $studyAreaGroup = $options['study_area_group'];
    assert($studyAreaGroup instanceof StudyAreaGroup);

    $builder
      ->add('name', TextType::class)
      ->add('studyAreas', EntityType::class, [
        'label'         => 'study-area.groups.areas',
        'class'         => StudyArea::class,
        'choice_label'  => 'name',
        'required'      => false,
        'by_reference'  => false,
        'multiple'      => true,
        'query_builder' => function (StudyAreaRepository $repo) use ($studyAreaGroup) {
          $qb = $repo->createQueryBuilder('s');
          $qb->where('s.group IS NULL');

          if ($studyAreaGroup->getId() !== null) {
            $qb->orWhere('s.group = :group')
              ->setParameter('group', $studyAreaGroup);
          }

          $qb->orderBy('s.name', 'ASC');

          return $qb;
        },
        'select2' => true,
      ])
      ->add('submit', SaveType::class, [
        'enable_save_and_list' => false,
        'enable_cancel'        => true,
        'cancel_label'         => 'form.discard',
        'cancel_route'         => 'app_studyarea_listgroups',
      ]);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setRequired('study_area_group');
    $resolver->setDefault('data_class', StudyAreaGroup::class);
    $resolver->setAllowedTypes('study_area_group', StudyAreaGroup::class);
  }
}
