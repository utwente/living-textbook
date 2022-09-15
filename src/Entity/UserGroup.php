<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserGroup.
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\UserGroupRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class UserGroup implements IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;
  final public const GROUP_REVIEWER = 'reviewer';
  final public const GROUP_EDITOR   = 'editor';
  final public const GROUP_VIEWER   = 'viewer';
  final public const GROUP_ANALYSIS = 'analysis';

  /**
   * @var StudyArea
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\StudyArea", inversedBy="userGroups")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * @var string
   *
   * @ORM\Column(name="group_type", type="string", length=10, nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\Choice(callback="getGroupTypes")
   */
  private $groupType;

  /**
   * @var Collection<User>
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="userGroups", fetch="EAGER")
   * @ORM\JoinTable(name="user_group_users",
   *   joinColumns={@ORM\JoinColumn(name="user_group_id", referencedColumnName="id")},
   *   inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
   * )
   *
   * @Assert\NotNull()
   */
  private $users;

  /**
   * @var Collection<UserGroupEmail>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\UserGroupEmail",
   *   mappedBy="userGroup", fetch="EAGER", cascade={"persist", "remove"})
   *
   * @Assert\NotNull()
   */
  private $emails;

  /** UserGroup constructor. */
  public function __construct()
  {
    $this->groupType = self::GROUP_VIEWER;
    $this->users     = new ArrayCollection();
    $this->emails    = new ArrayCollection();
  }

  /**
   * Possible group types.
   *
   * @return array
   */
  public static function getGroupTypes()
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

    $this->emails->add((new UserGroupEmail())->setEmail($email)->setUserGroup($this));

    return $this;
  }

  public function removeEmail(UserGroupEmail $email): UserGroup
  {
    $this->emails->removeElement($email);

    return $this;
  }
}
