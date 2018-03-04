<?php

namespace App\Form\StudyArea;


use App\Entity\StudyArea;
use App\Entity\User;
use App\Form\Type\SaveType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditStudyAreaType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $studyArea  = $options['studyArea'];
    $editing = $studyArea->getId() !== NULL;
    $builder
        ->add('name', TextType::class, [
            'label' => 'study-area.name',
        ])
        ->add('owner', EntityType::class, [
            'label' => 'study-area.owner',
            'class' => User::class,
        ])
        ->add('submit', SaveType::class, [
            'list_route'          => 'app_studyarea_list',
            'enable_cancel'       => true,
            'cancel_label'        => 'form.discard',
            'cancel_route'        => $editing ? 'app_studyarea_show' : 'app_studyarea_list',
            'cancel_route_params' => $editing ? ['studyArea' => $studyArea->getId()] : [],
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setRequired('studyArea');
    $resolver->setAllowedTypes('studyArea', StudyArea::class);
    parent::configureOptions($resolver);
  }

}