<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class StudyArea
 *
 * @author Tobias
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\StudyAreaRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class StudyArea
{

  // Access types, used to determine if a user can access the study area
  const ACCESS_PUBLIC = 'public';
  const ACCESS_PRIVATE = 'private';
  const ACCESS_GROUP = 'group';

  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255, nullable=false)
   * @Assert\NotBlank()
   * @Assert\Length(min=3, max=255)
   *
   * @JMSA\Expose()
   */
  private $name;

  /**
   * @var string|null
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   */
  private $description;

  /**
   * @var Collection|Concept[]
   *
   * @ORM\OneToMany(targetEntity="Concept", mappedBy="studyArea", cascade={"persist","remove"})
   *
   * @JMSA\Expose()
   */
  private $concepts;

  /**
   * @var Collection|UserGroup[]
   *
   * @ORM\OneToMany(targetEntity="App\Entity\UserGroup", mappedBy="studyArea", cascade={"persist","remove"})
   *
   * @JMSA\Expose()
   */
  private $userGroups;

  /**
   * @var User
   *
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="owner_user_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $owner;

  /**
   * @var string
   *
   * @ORM\Column(name="access_type", type="string", length=10, nullable=false)
   *
   * @Assert\Choice(callback="getAccessTypes")
   */
  private $accessType;

  /**
   * @var Collection|RelationType[]
   *
   * @ORM\OneToMany(targetEntity="App\Entity\RelationType", mappedBy="studyArea")
   */
  private $relationTypes;

  /**
   * @var Collection|Abbreviation[]
   *
   * @ORM\OneToMany(targetEntity="App\Entity\Abbreviation", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $abbreviations;

  /**
   * @var Collection|ExternalResource[]
   *
   * @ORM\OneToMany(targetEntity="App\Entity\ExternalResource", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $externalResources;

  /**
   * @var Collection|LearningOutcome[]
   *
   * @ORM\OneToMany(targetEntity="App\Entity\LearningOutcome", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $learningOutcomes;

  /**
   * StudyArea constructor.
   */
  public function __construct()
  {
    $this->name       = '';
    $this->concepts   = new ArrayCollection();
    $this->userGroups = new ArrayCollection();
    $this->accessType = self::ACCESS_PUBLIC;
  }

  /**
   * Possible access types
   *
   * @return array
   */
  public static function getAccessTypes()
  {
    return [self::ACCESS_PUBLIC, self::ACCESS_PRIVATE, self::ACCESS_GROUP];
  }

  /**
   * Check whether the user is in a certain or one of the groups
   *
   * @param User        $user
   * @param string|NULL $groupType
   *
   * @return bool
   */
  public function isUserInGroup(User $user, string $groupType = NULL)
  {
    foreach ($this->getUserGroups($groupType) as $userGroup) {
      return $userGroup->getUsers()->contains($user);
    }

    return false;
  }

  /**
   * @param string|NULL $groupType
   *
   * @return UserGroup[]|Collection
   */
  public function getUserGroups(string $groupType = NULL)
  {
    return $groupType === NULL ? $this->userGroups : $this->userGroups->matching(
        Criteria::create()->where(Criteria::expr()->eq('groupType', $groupType)));
  }

  /**
   * Get the editors
   *
   * @return User[]
   */
  public function getEditors()
  {
    $editorGroup = $this->getUserGroups(UserGroup::GROUP_EDITOR);
    if ($editorGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $users = array_merge(...$editorGroup->map(function (UserGroup $userGroup) {
      return $userGroup->getUsers()->toArray();
    })->toArray());
    usort($users, [User::class, 'sortOnDisplayName']);

    return $users;
  }

  /**
   * Get the editors which do not have an account (yet)
   *
   * @return UserGroupEmail[]
   */
  public function getEmailEditors()
  {
    $editorGroup = $this->getUserGroups(UserGroup::GROUP_EDITOR);
    if ($editorGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $userGroupEmails = array_merge(...$editorGroup->map(function (UserGroup $userGroup) {
      return $userGroup->getEmails()->toArray();
    })->toArray());
    usort($userGroupEmails, [UserGroupEmail::class, 'sortOnEmail']);

    return $userGroupEmails;
  }

  /**
   * Get the editors
   *
   * @return User[]
   */
  public function getReviewers()
  {
    $reviewGroup = $this->getUserGroups(UserGroup::GROUP_REVIEWER);
    if ($reviewGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $users = array_merge(...$reviewGroup->map(function (UserGroup $userGroup) {
      return $userGroup->getUsers()->toArray();
    })->toArray());
    usort($users, [User::class, 'sortOnDisplayName']);

    return $users;
  }

  /**
   * Get the reviewers which do not have an account (yet)
   *
   * @return UserGroupEmail[]
   */
  public function getEmailReviewers()
  {
    $reviewGroup = $this->getUserGroups(UserGroup::GROUP_REVIEWER);
    if ($reviewGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $userGroupEmails = array_merge(...$reviewGroup->map(function (UserGroup $userGroup) {
      return $userGroup->getEmails()->toArray();
    })->toArray());
    usort($userGroupEmails, [UserGroupEmail::class, 'sortOnEmail']);

    return $userGroupEmails;
  }

  /**
   * Get the viewers
   *
   * @return User[]
   */
  public function getViewers()
  {
    $viewerGroup = $this->getUserGroups(UserGroup::GROUP_VIEWER);
    if ($viewerGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $users = array_merge(...$viewerGroup->map(function (UserGroup $userGroup) {
      return $userGroup->getUsers()->toArray();
    })->toArray());
    usort($users, [User::class, 'sortOnDisplayName']);

    return $users;
  }

  /**
   * Get the viewers which do not have an account (yet)
   *
   * @return UserGroupEmail[]
   */
  public function getEmailViewers()
  {
    $viewerGroup = $this->getUserGroups(UserGroup::GROUP_VIEWER);
    if ($viewerGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $userGroupEmails = array_merge(...$viewerGroup->map(function (UserGroup $userGroup) {
      return $userGroup->getEmails()->toArray();
    })->toArray());
    usort($userGroupEmails, [UserGroupEmail::class, 'sortOnEmail']);

    return $userGroupEmails;
  }

  /**
   * Check whether the given user is the StudyArea owner
   *
   * @param User $user
   *
   * @return bool
   */
  public function isOwner(User $user)
  {
    return $user->getId() === $this->owner->getId();
  }

  /**
   * Check whether the StudyArea is visible for the user
   *
   * @param User $user
   *
   * @return bool
   */
  public function isVisible(User $user)
  {
    switch ($this->accessType) {
      case StudyArea::ACCESS_PUBLIC:
        return true;
      case StudyArea::ACCESS_PRIVATE:
        return $this->isOwner($user);
      case StudyArea::ACCESS_GROUP:
        return $this->isOwner($user) || $this->isUserInGroup($user);
    }

    return false;
  }

  public function isEditable(User $user)
  {
    return $this->isOwner($user) || $this->isUserInGroup($user, UserGroup::GROUP_EDITOR);
  }

  /**
   * @return array Array with DateTime and username
   */
  public function getLastEditInfo()
  {
    $lastUpdated   = $this->getLastUpdated();
    $lastUpdatedBy = $this->getLastUpdatedBy();

    // Loop relations to see if they have a newer date set
    $check = function ($entity) use (&$lastUpdated, &$lastUpdatedBy) {
      if ($entity instanceof Concept) {
        $lastEditInfo = $entity->getLastEditInfo();
        if ($lastEditInfo[0] > $lastUpdated) {
          $lastUpdated   = $lastEditInfo[0];
          $lastUpdatedBy = $lastEditInfo[1];
        }
      } else {
        /** @var Blameable $entity */
        if ($entity->getLastUpdated() > $lastUpdated) {
          $lastUpdated   = $entity->getLastUpdated();
          $lastUpdatedBy = $entity->getLastUpdatedBy();
        }
      }
    };

    // Check other data
    foreach ($this->getConcepts() as $concept) {
      $check($concept);
    }
    foreach ($this->getRelationTypes() as $relationType) {
      $check($relationType);
    }
    foreach ($this->abbreviations as $abbreviation) {
      $check($abbreviation);
    }
    foreach ($this->externalResources as $externalResource) {
      $check($externalResource);
    }
    foreach ($this->learningOutcomes as $learningOutcome) {
      $check($learningOutcome);
    }

    // Return result
    return [$lastUpdated, $lastUpdatedBy];
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   *
   * @return StudyArea
   */
  public function setName(string $name): StudyArea
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return null|string
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * @param null|string $description
   *
   * @return StudyArea
   */
  public function setDescription(?string $description): StudyArea
  {
    $this->description = $description;

    return $this;
  }

  /**
   * @return Concept[]|Collection
   */
  public function getConcepts()
  {
    return $this->concepts;
  }

  /**
   * @param Concept $concept
   *
   * @return StudyArea
   */
  public function addConcept(Concept $concept): StudyArea
  {
    // Check whether the study area is set, otherwise set it as this
    if (!$concept->getStudyArea()) {
      $concept->setStudyArea($this);
    }
    $this->concepts->add($concept);

    return $this;
  }

  /**
   * @param Concept $concept
   *
   * @return StudyArea
   */
  public function removeConcept(Concept $concept): StudyArea
  {
    $this->concepts->removeElement($concept);

    return $this;
  }

  /**
   * @param UserGroup $userGroup
   *
   * @return StudyArea
   */
  public function addUserGroup(UserGroup $userGroup): StudyArea
  {
    // Check whether the StudyArea is set, otherwise set it as this
    if (!$userGroup->getStudyArea()) {
      $userGroup->setStudyArea($this);
    }
    $this->userGroups->add($userGroup);

    return $this;
  }

  /**
   * @param UserGroup $userGroup
   *
   * @return StudyArea
   */
  public function removeUserGroup(UserGroup $userGroup): StudyArea
  {
    $this->userGroups->removeElement($userGroup);

    return $this;
  }

  /**
   * @return string
   */
  public function __toString(): string
  {
    return $this->getName();
  }

  /**
   * @return User|null
   */
  public function getOwner(): ?User
  {
    return $this->owner;
  }

  /**
   * @param User $owner
   *
   * @return StudyArea
   */
  public function setOwner(User $owner): StudyArea
  {
    $this->owner = $owner;

    return $this;
  }

  /**
   * @return string
   */
  public function getAccessType(): string
  {
    return $this->accessType;
  }

  /**
   * @param string $accessType
   *
   * @return StudyArea
   */
  public function setAccessType(string $accessType): StudyArea
  {
    $this->accessType = $accessType;

    return $this;
  }

  /**
   * @return RelationType[]|Collection
   */
  public function getRelationTypes()
  {
    return $this->relationTypes;
  }

}
