<?php

namespace App\Form\Type;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrintedTextType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $accessMethod = $options['access_method'];

    $builder->addViewTransformer(new CallbackTransformer(function ($dataValue) use ($accessMethod) {
      if ($dataValue != null && $accessMethod) {
        $dataValue = $dataValue->$accessMethod();
      }

      return $dataValue;
    }, function ($viewValue) {
      // This transformation is not required
    }));
  }

  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $view->vars['text_only'] = $options['text_only'];
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setDefaults([
        'access_method' => null,
        'disabled'      => true,
        'required'      => false,
        'text_only'     => false,
      ])
      ->setAllowedTypes('text_only', 'bool');
  }

  #[Override]
  public function getParent(): ?string
  {
    return TextType::class;
  }
}
