<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Entity\UserGroupEmail;
use InvalidArgumentException;

class UserPermissions
{
  /** @var User|null */
  private $user;
  /** @var UserGroupEmail|null */
  private $userGroupEmail;

  /** @var bool */
  private $viewer = false;
  /** @var bool */
  private $editor = false;
  /** @var bool */
  private $reviewer = false;
  /** @var bool */
  private $analysis = false;

  /** UserPermissions constructor. */
  public function __construct(?User $user, ?UserGroupEmail $userGroupEmail)
  {
    if (!$user && !$userGroupEmail) {
      throw new InvalidArgumentException('One of the constructor arguments must be set!');
    }
    if ($user && $userGroupEmail) {
      throw new InvalidArgumentException('Only one of the constructor arguments can be set!');
    }

    $this->user           = $user;
    $this->userGroupEmail = $userGroupEmail;
  }

  /** Add a permission based on the group. */
  public function addPermissionFromGroup(UserGroup $userGroup)
  {
    switch ($userGroup->getGroupType()) {
      case UserGroup::GROUP_VIEWER:
        $this->viewer = true;

        return;
      case UserGroup::GROUP_EDITOR:
        $this->editor = true;

        return;
      case UserGroup::GROUP_REVIEWER:
        $this->reviewer = true;

        return;
      case UserGroup::GROUP_ANALYSIS:
        $this->analysis = true;

        return;
    }

    throw new InvalidArgumentException(sprintf('Group type %s is not supported', $userGroup->getGroupType()));
  }

  public function isUser(): bool
  {
    return null !== $this->user;
  }

  public function isEmail(): bool
  {
    return !$this->isUser();
  }

  public function getDisplayName(): string
  {
    if ($this->isUser()) {
      return $this->user->getDisplayName();
    }

    return '';
  }

  /** @deprecated */
  public function getUsername(): string
  {
    return $this->getUserIdentifier();
  }

  public function getUserIdentifier(): string
  {
    if ($this->isUser()) {
      return $this->user->getUserIdentifier();
    }

    return $this->userGroupEmail->getEmail();
  }

  /** @return User|null */
  public function getUser(): ?User
  {
    return $this->user;
  }

  /** @return UserGroupEmail|null */
  public function getEmail(): ?UserGroupEmail
  {
    return $this->userGroupEmail;
  }

  /** @return bool */
  public function isViewer(): bool
  {
    return $this->viewer;
  }

  /** @return bool */
  public function isViewerOnly(): bool
  {
    return $this->isViewer() && !$this->isEditor() && !$this->isReviewer() && !$this->isAnalysis();
  }

  /** @return bool */
  public function isEditor(): bool
  {
    return $this->editor;
  }

  /** @return bool */
  public function isReviewer(): bool
  {
    return $this->reviewer;
  }

  /** @return bool */
  public function isAnalysis(): bool
  {
    return $this->analysis;
  }
}
