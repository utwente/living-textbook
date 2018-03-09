<?php

namespace App\Form\Data;

use App\Entity\ExternalResource;
use App\Form\Type\OrderedCollectionElementType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExternalResourceType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('id', HiddenType::class, [
            'disabled' => true,
        ])
        ->add('title', TextType::class, [
            'label' => 'resource.title',
        ])
        ->add('description', TextType::class, [
            'label' => 'resource.description',
        ])
        ->add('url', UrlType::class, [
            'label' => 'resource.url',
        ])
        ->add('position', OrderedCollectionElementType::class);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefault('data_class', ExternalResource::class);
    $resolver->setDefault('hide_label', true);
  }

}
