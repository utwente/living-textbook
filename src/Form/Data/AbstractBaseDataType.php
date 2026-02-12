<?php

namespace App\Form\Data;

use App\Entity\Data\BaseDataTextObject;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function class_exists;
use function class_uses;
use function in_array;
use function sprintf;

abstract class AbstractBaseDataType extends AbstractType
{
  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'label'      => false,
      'required'   => true,
      'hide_label' => true,
      'ckeditor'   => true,
    ]);
    $resolver->setAllowedTypes('label', ['string', 'bool']);
    $resolver->setAllowedTypes('required', ['bool']);
    $resolver->setAllowedTypes('ckeditor', ['bool']);

    $resolver->setRequired('data_class');
    $resolver->setAllowedTypes('data_class', ['string', 'null']);
    $resolver->setNormalizer('data_class', static function (Options $options, $value) {
      if (!class_exists($value)) {
        throw new InvalidConfigurationException(sprintf('The "data_class" option must contain a valid class name ("%s" given).', $value ?: 'NULL'));
      }

      $traits = class_uses($value);
      if (!in_array(BaseDataTextObject::class, $traits)) {
        throw new InvalidConfigurationException(sprintf('The "data_class" option must contain a class which uses the "%s" trait (class "%s" does not)', BaseDataTextObject::class, $value));
      }

      return $value;
    });
  }
}
