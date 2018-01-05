<?php

namespace App\Form\Data;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class JsonUploadType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('json', FileType::class, [
            'label' => 'data.json-file',
        ])
        ->add('submit', SubmitType::class);
  }
}
