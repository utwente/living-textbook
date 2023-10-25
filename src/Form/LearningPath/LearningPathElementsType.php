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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LearningPathElementsType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->addModelTransformer(new CallbackTransformer(
      fn (Collection $modelData): array => LearningPath::OrderElements($modelData)->toArray(),
      function (array $formData): ArrayCollection {
        /** @var LearningPathElement[] $formData */
        $previousElement = null;
        $formData        = array_values($formData);
        for ($i = count($formData) - 1; $i >= 0; $i--) {
          // Update the next element
          $formData[$i]->setNext($previousElement);
          if ($previousElement == null) {
            // Clear description for last element, as it is not possible to have it there
            $formData[$i]->setDescription(null);
          }
          $previousElement = $formData[$i];
        }

        return new ArrayCollection($formData);
      }
    ));
  }

  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['sortable_id'] = $options['sortable_id'];
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
      ->setRequired('studyArea')
      ->setAllowedTypes('studyArea', StudyArea::class)
      ->setRequired('learningPath')
      ->setAllowedTypes('learningPath', LearningPath::class)
      ->setRequired('sortable_id')
      ->setAllowedTypes('sortable_id', 'string')
      ->setDefaults([
        'by_reference' => false,
        'entry_type'   => LearningPathElementType::class,
        'allow_add'    => true,
        'allow_delete' => true,
      ]);

    $resolver->setNormalizer('entry_options', function (Options $options, $value) {
      $value['studyArea']    = $options->offsetGet('studyArea');
      $value['learningPath'] = $options->offsetGet('learningPath');
      $value['sortable_id']  = $options->offsetGet('sortable_id');

      return $value;
    });
  }

  public function getParent()
  {
    return CollectionType::class;
  }
}
