<?php

namespace App\Form\Analytics;

use App\Analytics\Model\SynthesizeRequest;
use App\Form\Type\RemoveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SynthesizeRequestType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $this->addNumberType($builder, 'usersPerfect');
    $this->addNumberType($builder, 'usersFlawed');
    $this->addNumberType($builder, 'usersFlawedTest');
    $this->addNumberType($builder, 'usersConceptBrowsers');
    $this->addNumberType($builder, 'usersConceptBrowsersTest');
    $this->addNumberType($builder, 'usersIgnore');
    $this->addNumberType($builder, 'usersTest');
    $this->addChanceType($builder, 'flawedDropOffChance');
    $this->addChanceType($builder, 'conceptBrowserDropOffChance');
    $this->addDaysBetweenType($builder, 'daysBetweenLearningPaths');
    $this->addDaysBetweenType($builder, 'daysBeforeTest');

    $builder
      ->add('testMoment', DateTimeType::class, [
        'label'            => 'analytics.synthesize-label.testMoment',
        'help'             => 'analytics.synthesize-help.testMoment',
        'full_width_label' => true,
        'required'         => true,
        'widget'           => 'single_text',
        'html5'            => true,
      ])
      ->add('submit', RemoveType::class, [
        'label'        => false,
        'cancel_route' => 'app_analytics_dashboard',
        'remove_label' => 'analytics.synthesize',
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => SynthesizeRequest::class,
    ]);
  }

  private function addNumberType(FormBuilderInterface $builder, string $field): void
  {
    $builder->add($field, NumberType::class, [
      'label'            => 'analytics.synthesize-label.' . $field,
      'help'             => 'analytics.synthesize-help.' . $field,
      'full_width_label' => true,
      'required'         => true,
      'html5'            => true,
      'scale'            => 0,
      'attr'             => [
        'min' => 0,
        'max' => 200,
      ],
    ]);
  }

  private function addDaysBetweenType(FormBuilderInterface $builder, string $field): void
  {
    $builder->add($field, NumberType::class, [
      'label'            => 'analytics.synthesize-label.' . $field,
      'help'             => 'analytics.synthesize-help.' . $field,
      'full_width_label' => true,
      'required'         => true,
      'html5'            => true,
      'scale'            => 0,
      'attr'             => [
        'min' => 1,
        'max' => 31,
      ],
    ]);
  }

  private function addChanceType(FormBuilderInterface $builder, string $field): void
  {
    $builder->add($field, NumberType::class, [
      'label'            => 'analytics.synthesize-label.' . $field,
      'help'             => 'analytics.synthesize-help.' . $field,
      'full_width_label' => true,
      'required'         => true,
      'html5'            => true,
      'scale'            => 3,
      'attr'             => [
        'min'  => 0,
        'max'  => 1,
        'step' => 0.001,
      ],
    ]);
  }
}
