<?php

namespace App\Form\Review;

use App\Entity\Review;
use App\Form\Type\SaveType;
use Doctrine\ORM\NonUniqueResultException;
use Override;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditReviewType extends AbstractReviewType
{
  /** @throws NonUniqueResultException */
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $this
      ->addNotes($builder)
      ->addReviewer($builder, $options);

    $builder
      ->add('submit', SaveType::class, [
        'enable_cancel'        => true,
        'cancel_route'         => 'app_review_submissions',
        'enable_save_and_list' => false,
        'save_label'           => $options['save_label'],
      ]);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    parent::configureOptions($resolver);

    $resolver
      ->setDefault('save_label', 'form.save')
      ->setDefault('data_class', Review::class)
      ->setAllowedTypes('save_label', 'string');
  }
}
