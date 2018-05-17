<?php

namespace App\Form\Data;

use App\Entity\StudyArea;
use App\Form\Type\CkEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseDataTextType extends AbstractBaseDataType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $formOptions = [
        'label'    => $options['label'],
        'required' => $options['required'],
    ];
    if ($options['ckeditor']) {
      $formOptions['studyArea'] = $options['studyArea'];
    }

    $builder
        ->add('text', $options['ckeditor'] ? CkEditorType::class : TextareaType::class, $formOptions);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);

    $resolver
        ->setDefaults([
            'ckeditor'  => true,
            'studyArea' => NULL,
        ])
        ->setAllowedTypes('ckeditor', ['bool'])
        ->setAllowedTypes('studyArea', ['null', StudyArea::class])
        ->setNormalizer('studyArea', function (Options $options, $value) {
          if ($options['ckeditor'] && NULL === $value) {
            throw new MissingOptionsException('The required option "studyArea" is missing (as the form is a ckeditor type).');
          }

          return $value;
        });
  }

}
