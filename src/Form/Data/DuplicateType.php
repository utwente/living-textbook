<?php

namespace App\Form\Data;

use App\Entity\Concept;
use App\Entity\StudyArea;
use App\Form\StudyArea\EditStudyAreaType;
use App\Form\Type\SaveType;
use App\Repository\ConceptRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class DuplicateType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    /** @var StudyArea $currentStudyArea */
    $currentStudyArea = $options['current_study_area'];

    $builder
        ->add('studyArea', EditStudyAreaType::class, [
            'studyArea'    => $options['new_study_area'],
            'select_owner' => false,
            'form_header'  => 'study-area.new',
            'hide_label'   => true,
            'hide_submit'  => true,
        ])
        ->add('concepts', EntityType::class, [
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
        ->add('select_all', CheckboxType::class, [
            'label'    => 'data.select-all',
            'required' => false,
            'help'     => 'data.select-all-info',
        ])
        ->add('submit', SaveType::class, [
            'list_route'           => 'app_concept_list',
            'enable_save'          => true,
            'save_label'           => 'data.duplicate',
            'save_icon'            => 'fa-copy',
            'enable_save_and_list' => false,
            'enable_list'          => false,
            'enable_cancel'        => true,
            'cancel_route'         => 'app_concept_list',
            'cancel_route_params'  => [],
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefaults(['constraints' => [
            new Callback(['callback' => [$this, 'checkConcepts']]),
        ]])
        ->setRequired('current_study_area')
        ->setRequired('new_study_area')
        ->setAllowedTypes('current_study_area', StudyArea::class)
        ->setAllowedTypes('new_study_area', StudyArea::class);
  }

  /**
   * Check if there is at least 1 concept selected to duplicate
   *
   * @param                           $data
   * @param ExecutionContextInterface $context
   */
  public function checkConcepts($data, ExecutionContextInterface $context)
  {
    if ($data['select_all'] === false && count($data['concepts']) === 0) {
      $context->buildViolation('data.concepts-no-selection')
          ->atPath('[concepts]')
          ->addViolation();
    }
  }

}
