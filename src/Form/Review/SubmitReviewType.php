<?php

namespace App\Form\Review;

use App\Entity\Review;
use App\Form\Type\SaveType;
use Doctrine\ORM\NonUniqueResultException;
use Override;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmitReviewType extends AbstractReviewType
{
  /** @throws NonUniqueResultException */
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        // This field will not be rendered by the SF form component in this field,
        // but by the ReviewSubmissionType field by setting the checkboxes to true
      ->add('pending_changes', CollectionType::class, [
        'required'      => false,
        'allow_add'     => true,
        'allow_delete'  => true,
        'entry_type'    => CollectionType::class,
        'entry_options' => [
          'required'     => false,
          'allow_add'    => true,
          'allow_delete' => true,
        ],
      ])

        // Re-use review submission type to show changes
      ->add('review', ReviewSubmissionType::class, [
        'mapped'     => false,
        'data'       => $options['review'],
        'review'     => false,
        'hide_label' => true,
        'checkboxes' => true,
      ]);

    $this
      ->addNotes($builder)
      ->addReviewer($builder, $options);

    $builder
      ->add('submit', SaveType::class, [
        'enable_cancel'        => false,
        'enable_save_and_list' => false,
      ]);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);

    $resolver
      ->setRequired('review')
      ->setAllowedTypes('review', Review::class);
  }
}
