<?php

namespace App\Form\LearningPath;

use App\Entity\LearningPath;
use App\Entity\StudyArea;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LearningPathElementContainerType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('selector', LearningPathElementSelectorType::class, [
            'mapped'      => false,
            'studyArea'   => $options['studyArea'],
            'sortable_id' => $options['sortable_id'],
        ])
        ->add('elements', LearningPathElementsType::class, [
            'studyArea'    => $options['studyArea'],
            'learningPath' => $options['learningPath'],
            'sortable_id'  => $options['sortable_id'],
        ]);

    $builder->addModelTransformer(new CallbackTransformer(
        fn (Collection $modelData): array => [
            'elements' => $modelData,
        ],
        fn (array $viewData): Collection => $viewData['elements']
    ));
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('studyArea')
        ->setAllowedTypes('studyArea', StudyArea::class)
        ->setRequired('learningPath')
        ->setAllowedTypes('learningPath', LearningPath::class)
        ->setDefault('sortable_id', '')
        ->setAllowedTypes('sortable_id', 'string');

    $resolver->setNormalizer('sortable_id', fn () => bin2hex(random_bytes(16)));
  }
}
