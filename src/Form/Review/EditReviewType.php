<?php

namespace App\Form\Review;

use App\Entity\Review;
use App\Form\Type\SaveType;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditReviewType extends AbstractReviewType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   *
   * @throws NonUniqueResultException
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $this
        ->addNotes($builder)
        ->addReviewer($builder, $options);

    $builder
        ->add('submit', SaveType::class, [
            'enable_cancel'        => true,
            'cancel_route'         => 'app_review_submissions',
            'enable_save_and_list' => false,
        ]);
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);

    $resolver
        ->setDefault('data_class', Review::class);
  }
}
