<?php

namespace App\Form\Data;

use App\Entity\Data\BaseDataTextObject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseDataTextType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('text', TextareaType::class, [
            'label'    => $options['label'],
            'required' => $options['required'],
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
        'label'      => false,
        'required'   => true,
        'hide_label' => true,
    ]);
    $resolver->setAllowedTypes('label', ['string', 'bool']);
    $resolver->setAllowedTypes('required', ['bool']);

    $resolver->setRequired('data_class');
    $resolver->setAllowedTypes('data_class', ['string', 'null']);
    $resolver->setNormalizer('data_class', function (OptionsResolver $options, $value) {
      if (!class_exists($value)) {
        throw new InvalidConfigurationException(sprintf('The "data_class" option must contain a valid class name ("%s" given).', $value ? $value : 'NULL'));
      }

      $traits = class_uses($value);
      if (!in_array(BaseDataTextObject::class, $traits)) {
        throw new InvalidConfigurationException(sprintf('The "data_class" option must contain a class which uses the "%s" trait (class "%s" does not)', BaseDataTextObject::class, $value));
      }

      return $value;
    });
  }

}
