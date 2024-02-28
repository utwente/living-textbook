<?php

namespace App\Form\RelationType;

use App\Form\Review\DisplayPendingChangeType;
use App\Form\Type\SaveType;
use App\Review\Model\PendingChangeObjectInfo;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditRelationTypeType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    /** @var PendingChangeObjectInfo $pendingChangeObjectInfo */
    $pendingChangeObjectInfo = $options['pending_change_info'];
    $disabledFields          = $pendingChangeObjectInfo->getDisabledFields();

    $builder
      ->add('name', TextType::class, [
        'label'    => 'relation-type.name',
        'disabled' => in_array('name', $disabledFields),
      ])
      ->add('name_review', DisplayPendingChangeType::class, [
        'field'               => 'name',
        'pending_change_info' => $pendingChangeObjectInfo,
      ])
      ->add('description', TextareaType::class, [
        'label'    => 'relation-type.description',
        'required' => false,
        'disabled' => in_array('description', $disabledFields),
      ])
      ->add('description_review', DisplayPendingChangeType::class, [
        'field'               => 'description',
        'pending_change_info' => $pendingChangeObjectInfo,
      ])
      ->add('submit', SaveType::class, [
        'enable_save_and_list' => false,
        'enable_cancel'        => true,
        'cancel_label'         => 'form.discard',
        'cancel_route'         => 'app_relationtype_list',
      ]);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
      ->setDefault('pending_change_info', new PendingChangeObjectInfo())
      ->setAllowedTypes('pending_change_info', PendingChangeObjectInfo::class);
  }
}
