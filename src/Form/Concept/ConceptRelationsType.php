<?php

namespace App\Form\Concept;

use App\Entity\Concept;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConceptRelationsType extends AbstractType
{
  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
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

    $resolver->setNormalizer('entry_options', function (Options $options, $value) {
      $value['concept']  = $options->offsetGet('concept');
      $value['incoming'] = $options->offsetGet('incoming');

      return $value;
    });
  }

  /** @suppress PhanTypeMismatchProperty */
  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $view->vars['allow_move'] = false;
  }

  #[Override]
  public function getParent(): ?string
  {
    return CollectionType::class;
  }
}
