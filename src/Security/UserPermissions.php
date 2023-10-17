<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Entity\UserGroupEmail;
use InvalidArgumentException;

class UserPermissions
{
  private ?User $user                     = null;
  private ?UserGroupEmail $userGroupEmail = null;

  private bool $viewer   = false;
  private bool $editor   = false;
  private bool $reviewer = false;
  private bool $analysis = false;

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

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function getEmail(): ?UserGroupEmail
  {
    return $this->userGroupEmail;
  }

  public function isViewer(): bool
  {
    return $this->viewer;
  }

  public function isViewerOnly(): bool
  {
    return $this->isViewer() && !$this->isEditor() && !$this->isReviewer() && !$this->isAnalysis();
  }

  public function isEditor(): bool
  {
    return $this->editor;
  }

  public function isReviewer(): bool
  {
    return $this->reviewer;
  }

  public function isAnalysis(): bool
  {
    return $this->analysis;
  }
}
