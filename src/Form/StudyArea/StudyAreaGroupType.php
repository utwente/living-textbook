<?php

namespace App\Form\StudyArea;

use App\Entity\StudyAreaGroup;
use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudyAreaGroupType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name', TextType::class)
        ->add('submit', SaveType::class, [
            'enable_save_and_list' => false,
            'enable_cancel'        => true,
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_studyarea_listgroups',
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefault('data_class', StudyAreaGroup::class);
  }


}
