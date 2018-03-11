<?php

namespace App\Form\Data;

use App\Form\Type\CkEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseDataTextType extends AbstractBaseDataType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('text', $options['ckeditor'] ? CkEditorType::class : TextareaType::class, [
            'label'    => $options['label'],
            'required' => $options['required'],
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);

    $resolver->setDefaults([
        'ckeditor' => true,
    ]);
    $resolver->setAllowedTypes('ckeditor', ['bool']);
  }

}
