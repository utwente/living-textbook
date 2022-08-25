<?php

namespace App\Database;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SoftDeletableSubscriber implements EventSubscriber
{
  /** Field name for deleted by */
  final public const FIELD_NAME = 'deletedBy';

  /** SoftDeletableSubscriber constructor. */
  public function __construct(private readonly TokenStorageInterface $tokenStorage)
  {
  }

  /**
   * Returns an array of events this subscriber wants to listen to.
   *
   * @return array
   */
  public function getSubscribedEvents()
  {
    return [SoftDeleteableListener::PRE_SOFT_DELETE];
  }

  /** Sets the deletedBy field. */
  public function preSoftDelete(LifecycleEventArgs $args)
  {
    // Get needed objects
    $object = $args->getObject();
    $om     = $args->getObjectManager();
    assert($om instanceof EntityManagerInterface);
    $uow    = $om->getUnitOfWork();

    // Get old field value
    $meta     = $om->getClassMetadata($object::class);
    $reflProp = $meta->getReflectionProperty(self::FIELD_NAME);
    $oldValue = $reflProp->getValue($object);

    // Update the value
    $user = $this->tokenStorage->getToken()->getUserIdentifier();
    $reflProp->setValue($object, $user);

    // Make sure the unit of works knows about this
    $uow->propertyChanged($object, self::FIELD_NAME, $oldValue, $user);
    $uow->scheduleExtraUpdate($object, [
        self::FIELD_NAME => [$oldValue, $user],
    ]);
  }
}
