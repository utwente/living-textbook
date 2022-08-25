<?php

namespace App\Form\Tag;

use App\Entity\StudyArea;
use App\Entity\Tag;
use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditTagType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name', TextType::class, [
            'label' => 'tag.name',
        ])
        ->add('color', ColorType::class, [
            'label' => 'tag.color',
        ])
        ->add('submit', SaveType::class, [
            'enable_cancel'        => true,
            'enable_save_and_list' => false,
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_tag_list',
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('studyArea')
        ->setAllowedTypes('studyArea', StudyArea::class)
        ->setDefault('data_class', Tag::class);
  }
}
