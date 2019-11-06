<?php

namespace App\Form\Review;

use App\Form\Type\SaveType;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class SubmitReviewType extends AbstractReviewType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   *
   * @throws NonUniqueResultException
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        // This field will no be rendered by the SF form component!
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
}
