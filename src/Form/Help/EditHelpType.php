<?php

namespace App\Form\Help;

use App\Entity\Help;
use App\Form\Type\CkEditorType;
use App\Form\Type\SaveType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditHelpType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('content', CkEditorType::class, [
        'label'       => 'help.content',
        'required'    => true,
        'config_name' => 'ltb_help',
      ])
      ->add('submit', SaveType::class, [
        'enable_cancel'        => true,
        'enable_save_and_list' => false,
        'cancel_label'         => 'form.discard',
        'cancel_route'         => 'app_help_index',
      ]);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefault('data_class', Help::class);
  }
}
