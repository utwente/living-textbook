<?php

namespace App\Serializer\Handler;

use App\Entity\Concept;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use PhpOption\Option;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ConceptHandler implements EventSubscriberInterface
{

  private $router;

  /**
   * ImageHandler constructor.
   *
   * @param RouterInterface $router
   */
  public function __construct(RouterInterface $router)
  {
    $this->router = $router;
  }

  /**
   * @return array
   */
  public static function getSubscribedEvents()
  {
    return array(
        array(
            'event'  => Events::PRE_SERIALIZE,
            'class'  => Concept::class,
            'method' => 'serializeConceptToJson',
        ),
    );
  }


  /**
   * @param PreSerializeEvent $event
   */
  public function serializeConceptToJson(PreSerializeEvent $event)
  {
    // Check for download_json group
    $correctGroup = $event->getContext()->getAttribute('groups')->map(function ($value) {
      return is_array($value) && in_array('download_json', $value);
    })->orElse(Option::fromValue(false));
    if (!$correctGroup) {
      return;
    }

    $concept = $event->getObject();
    assert($concept instanceof Concept);

    $basePath = $this->router->generate('app_concept_show', ['concept' => $concept->getId()]);
    $concept->setLink($this->router->generate('_home_simple', ['pageUrl' => substr($basePath, 1)], UrlGeneratorInterface::ABSOLUTE_URL));
  }
} 
