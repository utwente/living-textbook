<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailListType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->addModelTransformer(new CallbackTransformer(
            function (?array $transform) {
              if ($transform === NULL) return '';

              return implode(PHP_EOL, $transform);
            },
            function (?string $reverseTransform) {
              if ($reverseTransform === NULL) return [];

              $emails = array_map(function ($email) {
                return mb_strtolower(trim($email));
              }, preg_split('/\r\n|\r|\n|,/', $reverseTransform));

              return array_filter($emails, function ($email) {
                return strlen($email) > 0;
              });
            }
        ));
  }

  public function getParent()
  {
    return TextareaType::class;
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefault('attr', ['rows' => 10]);
  }


}
