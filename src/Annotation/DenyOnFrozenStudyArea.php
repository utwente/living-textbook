<?php

namespace App\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;

/**
 * Place this annotation on a method which should not be invoked when a study area is frozen. Handled by
 * App\Security\DenyOnFrozenStudyAreaSubscriber, depends on the FrameworkExtraBundle to register it in the request
 * attributes.
 *
 * @Annotation
 * @Target("METHOD")
 */
class DenyOnFrozenStudyArea implements ConfigurationInterface
{

  /**
   * Key which is used in the request attributes array
   *
   * @var string
   */
  public const KEY = 'deny_on_frozen_study_area';

  /**
   * @Required
   * @var string
   */
  public $route;

  /**
   * @var array
   */
  public $routeParams = [];

  /**
   * @Required
   * @var string
   */
  public $subject;

  /**
   * @return string
   */
  public function getRoute(): string
  {
    return $this->route;
  }

  /**
   * @return array
   */
  public function getRouteParams(): array
  {
    return $this->routeParams;
  }

  /**
   * @return string
   */
  public function getSubject(): string
  {
    return $this->subject;
  }

  /**
   * {@inheritdoc}
   */
  public function getAliasName()
  {
    return self::KEY;
  }

  /**
   * {@inheritdoc}
   */
  public function allowArray()
  {
    return false;
  }
}
