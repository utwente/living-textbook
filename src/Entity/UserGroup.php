<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Repository\UserGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserGroupRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class UserGroup implements IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;
  final public const string GROUP_REVIEWER = 'reviewer';
  final public const string GROUP_EDITOR   = 'editor';
  final public const string GROUP_VIEWER   = 'viewer';
  final public const string GROUP_ANALYSIS = 'analysis';

  #[Assert\NotNull]
  #[ORM\ManyToOne(inversedBy: 'userGroups')]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  private ?StudyArea $studyArea = null;

  #[Assert\NotNull]
  #[Assert\Choice(callback: 'getGroupTypes')]
  #[ORM\Column(name: 'group_type', length: 10, nullable: false)]
  private string $groupType = self::GROUP_VIEWER;

  /** @var Collection<User> */
  #[Assert\NotNull]
  #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'userGroups', fetch: 'EAGER')]
  #[ORM\JoinTable(name: 'user_group_users', joinColumns: [new ORM\JoinColumn(name: 'user_group_id', referencedColumnName: 'id')], inverseJoinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')])]
  private Collection $users;

  /** @var Collection<UserGroupEmail> */
  #[Assert\NotNull]
  #[ORM\OneToMany(mappedBy: 'userGroup', targetEntity: UserGroupEmail::class, cascade: ['persist', 'remove'], fetch: 'EAGER')]
  private Collection $emails;

  public function __construct()
  {
    $this->users     = new ArrayCollection();
    $this->emails    = new ArrayCollection();
  }

  /** Possible group types. */
  public static function getGroupTypes(): array
  {
    return [self::GROUP_VIEWER, self::GROUP_EDITOR, self::GROUP_REVIEWER, self::GROUP_ANALYSIS];
  }

  public function getStudyArea(): StudyArea
  {
    return $this->studyArea;
  }

  public function setStudyArea(StudyArea $studyArea): UserGroup
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  public function getGroupType(): string
  {
    return $this->groupType;
  }

  public function setGroupType(string $groupType): UserGroup
  {
    $this->groupType = $groupType;

    return $this;
  }

  /** @return Collection<User> */
  public function getUsers(): Collection
  {
    return $this->users;
  }

  public function addUser(User $user): UserGroup
  {
    if ($this->users->contains($user)) {
      return $this;
    }

    $this->users->add($user);

    return $this;
  }

  public function removeUser(User $user): UserGroup
  {
    $this->users->removeElement($user);

    return $this;
  }

  /** @return Collection<UserGroupEmail> */
  public function getEmails(): Collection
  {
    return $this->emails;
  }

  public function addEmail(string $email): UserGroup
  {
    // Check whether this is not a duplicate
    foreach ($this->emails as $userGroupEmail) {
      if ($userGroupEmail->getEmail() == $email) {
        return $this;
      }
    }

    $this->emails->add(new UserGroupEmail()->setEmail($email)->setUserGroup($this));

    return $this;
  }

  public function removeEmail(UserGroupEmail $email): UserGroup
  {
    $this->emails->removeElement($email);

    return $this;
  }
}
