<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrintedTextType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $accessMethod = $options['access_method'];

    $builder->addViewTransformer(new CallbackTransformer(function ($dataValue) use ($accessMethod) {
      if ($dataValue != NULL && $accessMethod) {
        $dataValue = $dataValue->$accessMethod();
      }

      return $dataValue;
    }, function ($viewValue) {
      // This transformation is not required
    }));
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefaults([
            'access_method' => NULL,
            'disabled'      => true,
            'required'      => false,
        ]);
  }

  public function getParent()
  {
    return TextType::class;
  }

}
