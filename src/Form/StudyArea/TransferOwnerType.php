<?php

namespace App\Form\StudyArea;

use App\Form\Type\SaveType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;

class TransferOwnerType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('new_owner', EmailType::class, [
        'label'       => 'study-area.new-owner',
        'help'        => 'study-area.new-owner-help',
        'constraints' => [
          new Email(),
        ],
      ])
      ->add('submit', SaveType::class, [
        'enable_save_and_list' => false,
        'enable_cancel'        => true,
        'cancel_route'         => 'app_studyarea_list',
      ]);
  }
}
