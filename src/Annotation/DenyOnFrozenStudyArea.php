<?php

namespace App\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 * Place this annotation on a method which should not be invoked when a study area is frozen. Handled by App\Security\FreezeSubscriber.
 */
class DenyOnFrozenStudyArea implements ConfigurationInterface
{

  /**
   * {@inheritdoc}
   */
  public function getAliasName()
  {
    return 'deny_on_frozen_study_area';
  }

  /**
   * {@inheritdoc}
   */
  public function allowArray()
  {
    return false;
  }
}