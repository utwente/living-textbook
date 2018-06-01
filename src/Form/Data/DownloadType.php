<?php

namespace App\Form\Data;

use App\Form\Type\SingleSubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DownloadType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        // In the future, there will probably be a type selection here (json/owl)
        ->add('submit', SingleSubmitType::class, [
            'label' => 'data.download',
            'icon'  => 'fa-download',
            'attr'  => array(
                'class' => 'btn btn-outline-success',
            ),
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
        'attr' => [
            'target' => '_blank',
        ],
    ]);
  }


}
