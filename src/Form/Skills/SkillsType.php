<?php

namespace App\Form\Skills;

use App\Entity\Skills\SkillSetEnum;
use App\Entity\Skills\SkillsInterface;
use App\Skills\SkillAccessor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<SkillsInterface> */
class SkillsType extends AbstractType
{
  public function __construct(
    private readonly SkillAccessor $skillAccessor,
  ) {
  }

  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    foreach ($options['skill_set']->getSkills() as $skill) {
      $builder->add($this->skillAccessor->propertyName($skill), TextareaType::class, [
        'label'    => $skill->label(),
        'translation_domain' => 'messages',
        'required' => false,
      ]);
    }
  }

  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $view->vars['skill_set'] = $options['skill_set'];
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setDefault('data_class', SkillsInterface::class)
      ->setRequired('skill_set')
      ->setAllowedTypes('skill_set', SkillSetEnum::class);
  }
}
