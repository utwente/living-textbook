<?php

namespace App\Form\Abbreviation;

use App\Entity\Abbreviation;
use App\Entity\StudyArea;
use App\Form\Review\DisplayPendingChangeType;
use App\Form\Type\SaveType;
use App\Review\Model\PendingChangeObjectInfo;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAbbreviationType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    /** @var PendingChangeObjectInfo $pendingChangeObjectInfo */
    $pendingChangeObjectInfo = $options['pending_change_info'];
    $disabledFields          = $pendingChangeObjectInfo->getDisabledFields();

    $builder
      ->add('abbreviation', TextType::class, [
        'label'    => 'abbreviation.abbreviation',
        'disabled' => in_array('abbreviation', $disabledFields),
      ])
      ->add('abbreviation_review', DisplayPendingChangeType::class, [
        'field'               => 'abbreviation',
        'pending_change_info' => $pendingChangeObjectInfo,
      ])
      ->add('meaning', TextType::class, [
        'label'    => 'abbreviation.meaning',
        'disabled' => in_array('meaning', $disabledFields),
      ])
      ->add('meaning_review', DisplayPendingChangeType::class, [
        'field'               => 'meaning',
        'pending_change_info' => $pendingChangeObjectInfo,
      ])
      ->add('submit', SaveType::class, [
        'enable_cancel'        => true,
        'enable_save_and_list' => false,
        'cancel_label'         => 'form.discard',
        'cancel_route'         => 'app_abbreviation_list',
      ]);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
      ->setRequired('studyArea')
      ->setDefault('pending_change_info', new PendingChangeObjectInfo())
      ->setAllowedTypes('studyArea', StudyArea::class)
      ->setAllowedTypes('pending_change_info', PendingChangeObjectInfo::class)
      ->setDefault('data_class', Abbreviation::class);
  }
}
