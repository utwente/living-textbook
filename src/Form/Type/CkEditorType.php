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
    $resolver
        ->setDefault('studyArea', NULL)
        ->setAllowedTypes('studyArea', ['null', StudyArea::class])
        ->setDefault('config', function (Options $options) {
          if ($options['studyArea'] === NULL) {
            return [];
          }

          return [
              'filebrowserBrowseRouteParameters' => [
                // If not given, route generation will fail. In case of 0, the button should be hidden anyways
                  'studyAreaId' => $options['studyArea']->getId() ?? 0,
              ],
          ];
        })
        ->setNormalizer('config_name', function (Options $options, $value) {
          if ($value === 'ltb_config' && ($options['studyArea'] !== NULL && $options['studyArea']->getId() === NULL)) {
            return 'ltb_no_image';
          }

          return $value;
        });
  }

  public function getParent()
  {
    return \FOS\CKEditorBundle\Form\Type\CKEditorType::class;
  }
}
