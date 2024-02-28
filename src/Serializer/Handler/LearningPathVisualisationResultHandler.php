<?php

namespace App\Serializer\Handler;

use App\Analytics\Model\LearningPathVisualisationResult;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Override;
use Symfony\Component\Finder\SplFileInfo;

class LearningPathVisualisationResultHandler implements EventSubscriberInterface
{
  private const string EMPTY = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';

  #[Override]
  public static function getSubscribedEvents()
  {
    return [
      [
        'event'  => Events::POST_SERIALIZE,
        'class'  => LearningPathVisualisationResult::class,
        'format' => 'json',
        'method' => 'onPostSerialize',
      ],
    ];
  }

  public function onPostSerialize(ObjectEvent $event)
  {
    $visitor = $event->getVisitor();
    if (!$visitor instanceof SerializationVisitorInterface) {
      return;
    }

    $object = $event->getObject();
    assert($object instanceof LearningPathVisualisationResult);
    if (!$object) {
      return;
    }

    $visitor->visitProperty(
      new StaticPropertyMetadata('', 'heatMap', null),
      self::toBase64($object->heatMapImage));
    $visitor->visitProperty(
      new StaticPropertyMetadata('', 'pathVisits', null),
      self::toBase64($object->pathVisitsImage));
    $visitor->visitProperty(
      new StaticPropertyMetadata('', 'pathUsers', null),
      self::toBase64($object->pathUsersImage));
    $visitor->visitProperty(
      new StaticPropertyMetadata('', 'flowThrough', null),
      json_decode($object->flowThroughFile->getContents(), true));
    $visitor->visitProperty(
      new StaticPropertyMetadata('', 'metadata', null),
      json_decode($object->metaDataFile->getContents(), true));
  }

  private static function toBase64(?SplFileInfo $file)
  {
    if (!$file) {
      return self::EMPTY;
    }

    return 'data:image/' . mb_strtolower($file->getExtension()) . ';base64,' . base64_encode($file->getContents());
  }
}
