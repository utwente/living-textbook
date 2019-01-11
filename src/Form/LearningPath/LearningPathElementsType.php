<?php

namespace App\Form\LearningPath;

use App\Entity\LearningPath;
use App\Entity\LearningPathElement;
use App\Entity\StudyArea;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LearningPathElementsType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->addModelTransformer(new CallbackTransformer(
        function (Collection $modelData): array {
          return $modelData->toArray();
        },
        function (array $formData) use ($options) : ArrayCollection {
          /** @var LearningPathElement[] $formData */
          $previousElement = NULL;
          for ($i = count($formData) - 1; $i >= 0; $i--) {
            // Update the next element
            $formData[$i]->setNext($previousElement);
            $previousElement = $formData[$i];
          }

          return new ArrayCollection($formData);
        }
    ));
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('studyArea')
        ->setAllowedTypes('studyArea', StudyArea::class)
        ->setRequired('learningPath')
        ->setAllowedTypes('learningPath', LearningPath::class)
        ->setDefaults([
            'by_reference' => false,
            'entry_type'   => LearningPathElementType::class,
            'allow_add'    => true,
            'allow_delete' => true,
        ]);

    $resolver->setNormalizer('entry_options', function (Options $options, $value) {
      $value['studyArea']    = $options->offsetGet('studyArea');
      $value['learningPath'] = $options->offsetGet('learningPath');

      return $value;
    });
  }

  public function getParent()
  {
    return CollectionType::class;
  }

}
