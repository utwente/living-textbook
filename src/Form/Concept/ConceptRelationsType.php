<?php

namespace App\Form\Concept;

use App\Entity\Concept;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConceptRelationsType extends AbstractType
{
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setRequired('concept');
    $resolver->setDefaults([
        'incoming'       => false,
        'allow_add'      => true,
        'allow_delete'   => true,
        'prototype'      => true,
        'error_bubbling' => false,
        'entry_type'     => ConceptRelationType::class,
        'entry_options'  => [
            'hide_label' => true,
            'incoming'   => false,
        ],
    ]);

    $resolver->setAllowedTypes('concept', [Concept::class]);
    $resolver->setAllowedTypes('incoming', ['bool']);

    $resolver->setNormalizer('entry_options', function (OptionsResolver $options, $value) {
      $value['concept']  = $options->offsetGet('concept');
      $value['incoming'] = $options->offsetGet('incoming');

      return $value;
    });
  }

  /**
   * @param FormView      $view
   * @param FormInterface $form
   * @param array         $options
   *
   * @suppress PhanTypeMismatchProperty
   */
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['allow_move'] = false;
  }

  public function getParent()
  {
    return CollectionType::class;
  }
}
