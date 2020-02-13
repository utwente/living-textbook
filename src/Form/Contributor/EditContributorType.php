<?php

namespace App\Form\Contributor;

use App\Entity\Contributor;
use App\Entity\StudyArea;
use App\Form\Review\DisplayPendingChangeType;
use App\Form\Type\SaveType;
use App\Review\Model\PendingChangeObjectInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditContributorType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    /** @var PendingChangeObjectInfo $pendingChangeObjectInfo */
    $pendingChangeObjectInfo = $options['pending_change_info'];
    $disabled_fields         = $pendingChangeObjectInfo->getDisabledFields();

    $builder
        ->add('name', TextType::class, [
            'label'    => 'contributor.name',
            'disabled' => in_array('name', $disabled_fields),
        ])
        ->add('name_review', DisplayPendingChangeType::class, [
            'field'               => 'name',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('description', TextType::class, [
            'empty_data' => '',
            'label'      => 'contributor.description',
            'required'   => false,
            'disabled'   => in_array('description', $disabled_fields),
        ])
        ->add('description_review', DisplayPendingChangeType::class, [
            'field'               => 'description',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('url', UrlType::class, [
            'label'    => 'contributor.url',
            'required' => false,
            'disabled' => in_array('url', $disabled_fields),
        ])
        ->add('url_review', DisplayPendingChangeType::class, [
            'field'               => 'url',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('submit', SaveType::class, [
            'enable_cancel'        => true,
            'enable_save_and_list' => false,
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_contributor_list',
        ]);
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('studyArea')
        ->setAllowedTypes('studyArea', StudyArea::class)
        ->setDefault('data_class', Contributor::class)
        ->setDefault('pending_change_info', new PendingChangeObjectInfo())
        ->setAllowedTypes('pending_change_info', PendingChangeObjectInfo::class);
  }
}
