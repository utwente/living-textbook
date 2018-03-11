<?php

namespace App\Form\Data;

use App\Entity\Data\DataExternalResources;
use App\Form\Type\OrderedCollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataExternalResourcesType extends AbstractBaseDataType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('resources', OrderedCollectionType::class, [
            'label'        => $options['label'],
            'required'     => $options['required'],
            'allow_add'    => true,
            'allow_delete' => true,
            'prototype'    => true,
            'entry_type'   => ExternalResourceType::class,
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);

    // Set default for data_class and disable normalizer
    $resolver->setDefault('data_class', DataExternalResources::class);
    $resolver->setNormalizer('data_class', function (OptionsResolver $options, $value) {
      return $value;
    });
  }

}
