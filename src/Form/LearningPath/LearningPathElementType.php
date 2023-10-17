<?php

namespace App\Form\LearningPath;

use App\Entity\Concept;
use App\Entity\LearningPath;
use App\Entity\LearningPathElement;
use App\Entity\StudyArea;
use App\Form\Type\PrintedTextType;
use App\Repository\ConceptRepository;
use App\Repository\LearningPathElementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class LearningPathElementType extends AbstractType
{
  private ConceptRepository $conceptRepository;

  private LearningPathElementRepository $learningPathElementRepository;

  public function __construct(ConceptRepository $conceptRepository, LearningPathElementRepository $learningPathElementRepository)
  {
    $this->conceptRepository             = $conceptRepository;
    $this->learningPathElementRepository = $learningPathElementRepository;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('id', HiddenType::class)
        ->add('conceptId', HiddenType::class, [
            'constraints' => new Callback(function ($value, ExecutionContextInterface $context) use ($options) {
              // Verify whether the supplied id actually exists
              if (!$this->getConcept($value, $options['studyArea'])) {
                $context->buildViolation('Error')
                    ->addViolation();
              }
            }),
        ])
        ->add('concept', PrintedTextType::class, [
            'label' => 'learning-path.element-concept',
        ])
        ->add('description', TextareaType::class, [
            'required'    => false,
            'label'       => 'learning-path.element-description',
            'constraints' => [
                new Length(['max' => 1024]),
            ],
        ]);

    $builder->addModelTransformer(new CallbackTransformer(
        function (?LearningPathElement $modelData): array {
          $concept = $modelData == null ? null : $modelData->getConcept();

          return [
              'id'          => $modelData == null ? null : $modelData->getId(),
              'conceptId'   => $concept == null ? null : $concept->getId(),
              'concept'     => $concept == null ? null : $concept->getName(),
              'description' => $modelData == null ? null : $modelData->getDescription(),
          ];
        },
        function (array $viewData) use ($options): ?LearningPathElement {
          $concept               = $this->getConcept(intval($viewData['conceptId']), $options['studyArea']);
          $learningPathElementId = intval($viewData['id']);
          $element               = $learningPathElementId === -1 || empty($learningPathElementId)
              ? new LearningPathElement()
              : $this->learningPathElementRepository->findOneBy(['id' => $learningPathElementId, 'learningPath' => $options['learningPath']]);

          return $element
              ->setLearningPath($options['learningPath'])
              ->setConcept($concept)
              ->setDescription($viewData['description']);
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
        ->setAllowedTypes('sortable_id', 'string');
  }

  private function getConcept(?int $id, StudyArea $studyArea): ?Concept
  {
    if ($id == null) {
      return null;
    }

    $concept = $this->conceptRepository->findOneBy(['id' => $id, 'studyArea' => $studyArea]);
    assert($concept == null || $concept instanceof Concept);

    return $concept;
  }
}
