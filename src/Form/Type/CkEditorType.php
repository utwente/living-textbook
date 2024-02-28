<?php

namespace App\Form\Type;

use App\Entity\StudyArea;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CkEditorType
 * Extended CkEditorType to control default configuration.
 *
 * @author BobV
 */
class CkEditorType extends AbstractType
{
  #[Override]
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
      ->setDefault('studyArea', null)
      ->setAllowedTypes('studyArea', ['null', StudyArea::class])
      ->setDefault('config', function (Options $options) {
        if ($options['studyArea'] === null) {
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
        if ($value === 'ltb_config' && ($options['studyArea'] !== null && $options['studyArea']->getId() === null)) {
          return 'ltb_no_image';
        }

        return $value;
      });
  }

  #[Override]
  public function getParent()
  {
    return \FOS\CKEditorBundle\Form\Type\CKEditorType::class;
  }
}
