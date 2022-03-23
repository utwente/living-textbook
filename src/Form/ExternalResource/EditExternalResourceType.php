<?php

namespace App\Form\ExternalResource;

use App\Entity\ExternalResource;
use App\Entity\StudyArea;
use App\Form\Review\DisplayPendingChangeType;
use App\Form\Type\SaveType;
use App\Review\Model\PendingChangeObjectInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditExternalResourceType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    /** @var PendingChangeObjectInfo $pendingChangeObjectInfo */
    $pendingChangeObjectInfo = $options['pending_change_info'];
    $disabledFields          = $pendingChangeObjectInfo->getDisabledFields();

    $builder
        ->add('title', TextType::class, [
            'label'    => 'external-resource.title',
            'disabled' => in_array('title', $disabledFields),
        ])
        ->add('title_review', DisplayPendingChangeType::class, [
            'field'               => 'title',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('description', TextType::class, [
            'empty_data' => '',
            'label'      => 'external-resource.description',
            'required'   => false,
            'disabled'   => in_array('description', $disabledFields),
        ])
        ->add('description_review', DisplayPendingChangeType::class, [
            'field'               => 'description',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('url', UrlType::class, [
            'label'    => 'external-resource.url',
            'required' => false,
            'disabled' => in_array('url', $disabledFields),
        ])
        ->add('url_review', DisplayPendingChangeType::class, [
            'field'               => 'url',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('submit', SaveType::class, [
            'enable_cancel'        => true,
            'enable_save_and_list' => false,
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_externalresource_list',
        ]);
  }

  /** @param OptionsResolver $resolver */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('studyArea')
        ->setAllowedTypes('studyArea', StudyArea::class)
        ->setDefault('data_class', ExternalResource::class)
        ->setDefault('pending_change_info', new PendingChangeObjectInfo())
        ->setAllowedTypes('pending_change_info', PendingChangeObjectInfo::class);
  }
}
