<?php

declare(strict_types=1);

namespace App\Form\Skills;

use App\Entity\Data\DataSkills;
use App\Entity\Skills\SkillSetEnum;
use App\Skills\SkillAccessor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<DataSkills> */
class DataSkillsType extends AbstractType
{
  public function __construct(
    private readonly SkillAccessor $skillAccessor,
  ) {
  }

  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    foreach (SkillSetEnum::cases() as $skillSet) {
      $builder
        ->add($this->skillAccessor->propertyName($skillSet), SkillsType::class, [
          'skill_set' => $skillSet,
        ]);
    }
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefault('data_class', DataSkills::class);
  }
}
