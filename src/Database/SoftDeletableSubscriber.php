<?php

namespace App\Database;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SoftDeletableSubscriber implements EventSubscriber
{

  /**
   * Field name for deleted by
   */
  const FIELD_NAME = 'deletedBy';

  /**
   * @var TokenStorageInterface
   */
  private $tokenStorage;

  /**
   * SoftDeletableSubscriber constructor.
   *
   * @param TokenStorageInterface $tokenStorage
   */
  public function __construct(TokenStorageInterface $tokenStorage)
  {
    $this->tokenStorage = $tokenStorage;
  }

  /**
   * Returns an array of events this subscriber wants to listen to.
   *
   * @return array
   */
  public function getSubscribedEvents()
  {
    return array(SoftDeleteableListener::PRE_SOFT_DELETE);
  }

  /**
   * Sets the deletedBy field
   *
   * @param LifecycleEventArgs $args
   */
  public function preSoftDelete(LifecycleEventArgs $args)
  {
    // Get needed objects
    $object = $args->getObject();
    $om     = $args->getEntityManager();
    $uow    = $args->getEntityManager()->getUnitOfWork();

    // Get old field value
    $meta     = $om->getClassMetadata(get_class($object));
    $reflProp = $meta->getReflectionProperty(self::FIELD_NAME);
    $oldValue = $reflProp->getValue($object);

    // Update the value
    $user = $this->tokenStorage->getToken()->getUsername();
    $reflProp->setValue($object, $user);

    // Make sure the unit of works knows about this
    $uow->propertyChanged($object, self::FIELD_NAME, $oldValue, $user);
    $uow->scheduleExtraUpdate($object, array(
        self::FIELD_NAME => array($oldValue, $user),
    ));
  }
}
