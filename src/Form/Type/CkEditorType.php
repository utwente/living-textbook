<?php

namespace App\Form\Type;

use App\Entity\StudyArea;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CkEditorType
 * Extended CkEditorType to control default configuration
 *
 * @author BobV
 */
class CkEditorType extends AbstractType
{
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setRequired('studyArea')
        ->setAllowedTypes('studyArea', StudyArea::class)
        ->setDefault('config', function (Options $options) {
          return [
              'filebrowserBrowseRouteParameters' => [
                  'studyArea' => $options['studyArea']->getId(),
              ],
          ];
        });
  }

  public function getParent()
  {
    return \FOS\CKEditorBundle\Form\Type\CKEditorType::class;
  }
}
