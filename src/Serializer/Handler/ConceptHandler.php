<?php

namespace App\Serializer\Handler;

use App\Entity\Concept;
use App\Router\LtbRouter;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class ConceptHandler implements EventSubscriberInterface
{

  private $router;

  /**
   * ImageHandler constructor.
   *
   * @param LtbRouter $router
   */
  public function __construct(LtbRouter $router)
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
    $groups       = $event->getContext()->getAttribute('groups');
    $correctGroup = is_array($groups) && in_array('download_json', $groups);
    if (!$correctGroup) {
      return;
    }

    $concept = $event->getObject();
    assert($concept instanceof Concept);

    $concept->setLink(
        $this->router->generateBrowserUrl('app_concept_show', ['concept' => $concept->getId()]));
  }
} 
