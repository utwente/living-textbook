<?php

namespace App\Review\Model;

use App\Entity\PendingChange;
use InvalidArgumentException;

class PendingChangeObjectInfo
{
  /** @var PendingChange[] */
  private $pendingChanges;

  /** @var string[] */
  private $disabledFields = [];

  /**
   * PendingChangeObjectInfo constructor.
   *
   * @param PendingChange[] $pendingChanges
   */
  public function __construct(array $pendingChanges = [])
  {
    $this->pendingChanges = $pendingChanges;

    if (0 === count($pendingChanges)) {
      return;
    }

    $first = $pendingChanges[0];

    foreach ($pendingChanges as $pendingChange) {
      if (!$pendingChange instanceof PendingChange) {
        throw new InvalidArgumentException(sprintf('All pending changes must be of type %s', PendingChange::class));
      }

      if ($first->getObjectType() !== $pendingChange->getObjectType()) {
        throw new InvalidArgumentException('All pending changes must have the same object type!');
      }

      if ($first->getObjectId() !== $pendingChange->getObjectId()) {
        throw new InvalidArgumentException('All pending changes must have the same object id!');
      }

      if ($pendingChange->getChangeType() !== PendingChange::CHANGE_TYPE_EDIT) {
        throw new InvalidArgumentException(sprintf('All pending changes must have the same %s type!', PendingChange::CHANGE_TYPE_EDIT));
      }

      // Retrieve the disabled fields
      $this->disabledFields = array_merge($this->disabledFields, $pendingChange->getChangedFields());
    }
  }

  /** @return bool */
  public function hasChanges(): bool
  {
    return count($this->pendingChanges) > 0;
  }

  public function hasChangesForField(string $field): bool
  {
    return in_array($field, $this->disabledFields);
  }

  public function getPendingChangeForField(string $field): PendingChange
  {
    if (!$this->hasChangesForField($field)) {
      throw new InvalidArgumentException(sprintf('Cannot retrieve pending change for field %s, as there are none', $field));
    }

    $pendingChange = array_values(array_filter($this->pendingChanges, function (PendingChange $pendingChange) use ($field) {
      return in_array($field, $pendingChange->getChangedFields());
    }));

    return $pendingChange[0];
  }

  /** @return string|null */
  public function getObjectType(): ?string
  {
    if (!$this->hasChanges()) {
      return null;
    }

    return $this->pendingChanges[0]->getObjectType();
  }

  /** @return PendingChange[] */
  public function getPendingChanges(): array
  {
    return $this->pendingChanges;
  }

  /** @return string[] */
  public function getDisabledFields(): array
  {
    return $this->disabledFields;
  }
}
