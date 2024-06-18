<?php

namespace App\Database;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsDoctrineListener(SoftDeleteableListener::PRE_SOFT_DELETE)]
class SoftDeletableSubscriber
{
  /** Field name for deleted by */
  final public const string FIELD_NAME = 'deletedBy';

  public function __construct(private readonly TokenStorageInterface $tokenStorage)
  {
  }

  /** Sets the deletedBy field. */
  public function preSoftDelete(LifecycleEventArgs $args): void
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
