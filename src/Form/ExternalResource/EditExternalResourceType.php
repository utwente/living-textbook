<?php

namespace App\Form\ExternalResource;

use App\Entity\ExternalResource;
use App\Entity\StudyArea;
use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditExternalResourceType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('title', TextType::class, [
            'label' => 'external-resource.title',
        ])
        ->add('description', TextType::class, [
            'empty_data' => '',
            'label'      => 'external-resource.description',
            'required'   => false,
        ])
        ->add('url', UrlType::class, [
            'label'    => 'external-resource.url',
            'required' => false,
        ])
        ->add('submit', SaveType::class, [
            'enable_cancel'        => true,
            'enable_save_and_list' => false,
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_externalresource_list',
        ]);
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('studyArea')
        ->setAllowedTypes('studyArea', StudyArea::class)
        ->setDefault('data_class', ExternalResource::class);
  }
}
