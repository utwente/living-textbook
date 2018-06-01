<?php

namespace App\Form\Data;

use App\Form\Type\SingleButtonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DownloadType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        // In the future, there will probably be a type selection here (json/owl)
        ->add('submit', SingleButtonType::class, [
            'label' => 'data.download',
            'icon'  => 'fa-download',
            'attr'  => array(
                'class' => 'btn btn-outline-success',
            ),
        ]);
  }
}
