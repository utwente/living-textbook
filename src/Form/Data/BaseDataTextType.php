<?php

namespace App\Form\Data;

use App\Entity\Data\BaseDataTextObject;
use App\Form\Type\CkEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
