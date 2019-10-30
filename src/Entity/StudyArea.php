<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Security\UserPermissions;
use App\Validator\Constraint\StudyAreaAccessType;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
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
   * @Assert\NotNull()
   * @StudyAreaAccessType()
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
   * @var Collection|Contributor[]
   *
   * @ORM\OneToMany(targetEntity="App\Entity\Contributor", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $contributors;

  /**
   * @var Collection|LearningOutcome[]
   *
   * @ORM\OneToMany(targetEntity="App\Entity\LearningOutcome", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $learningOutcomes;

  /**
   * @var Collection|LearningPath[]
   *
   * @ORM\OneToMany(targetEntity="App\Entity\LearningPath", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $learningPaths;

  /**
   * @var DateTime
   *
   * @ORM\Column(name="frozen_on", type="datetime", nullable=true)
   */
  private $frozenOn;

  /**
   * @var string|null
   *
   * @ORM\Column(name="print_header", type="string", length=100, nullable=true)
   *
   * @Assert\Length(max=100)
   */
  private $printHeader;

  /**
   * @var string|null
   *
   * @ORM\Column(name="print_introduction", type="text", nullable=true)
   */
  private $printIntroduction;

  /**
   * If set, user interaction will be tracked (with user opt-in)
   *
   * @var bool
   *
   * @ORM\Column(name="track_users", type="boolean", nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\Type("bool")
   */
  private $trackUsers;

  /**
   * Group
   *
   * @var StudyAreaGroup|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\StudyAreaGroup", inversedBy="studyAreas")
   * @ORM\JoinColumn(nullable=true)
   */
  private $group;

  /**
   * Open access
   *
   * @var bool
   *
   * @ORM\Column(type="boolean", nullable=false)
   */
  private $openAccess;

  /**
   * Analytics dashboard enabled
   *
   * @var bool
   *
   * @ORM\Column(type="boolean", nullable=false)
   */
  private $analyticsDashboardEnabled;

  /**
   * Whether the review mode has been enabled for this study area
   *
   * @var bool
   *
   * @ORM\Column(type="boolean", nullable=false)
   */
  private $reviewModeEnabled;

  /**
   * StudyArea constructor.
   */
  public function __construct()
  {
    $this->name                      = '';
    $this->concepts                  = new ArrayCollection();
    $this->userGroups                = new ArrayCollection();
    $this->accessType                = self::ACCESS_PRIVATE;
    $this->trackUsers                = false;
    $this->openAccess                = false;
    $this->analyticsDashboardEnabled = false;
    $this->reviewModeEnabled         = false;
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
   * Possible access types, depending on the access level
   *
   * @param AuthorizationCheckerInterface $authorizationChecker
   * @param EntityManagerInterface        $em
   *
   * @return array
   */
  public function getAvailableAccessTypes(AuthorizationCheckerInterface $authorizationChecker, EntityManagerInterface $em)
  {
    // Get original field value
    $origObj   = $em->getUnitOfWork()->getOriginalEntityData($this);
    $prevValue = array_key_exists('accessType', $origObj) ? $origObj['accessType'] : NULL;

    // Get choices, remove public type when not administrator, and field has changed
    $choices = StudyArea::getAccessTypes();
    if (!$authorizationChecker->isGranted("ROLE_SUPER_ADMIN") && $prevValue !== self::ACCESS_PUBLIC) {
      $choices = array_filter($choices, function ($item) {
        return $item !== StudyArea::ACCESS_PUBLIC;
      });
    }

    return $choices;
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
      if ($userGroup->getUsers()->contains($user)) return true;
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
   * Retrieve the available user group types
   *
   * @return array
   */
  public function getAvailableUserGroupTypes()
  {
    if ($this->getAccessType() === StudyArea::ACCESS_PRIVATE) {
      return [];
    }

    $result = [];
    if ($this->getAccessType() !== StudyArea::ACCESS_PUBLIC) {
      $result[] = UserGroup::GROUP_VIEWER;
    }

    $result[] = UserGroup::GROUP_EDITOR;

    // @todo: Enable with #82
//    if ($studyArea->isReviewEnabled()) {
    $result[] = UserGroup::GROUP_REVIEWER;
//    }

    if ($this->isAnalyticsDashboardEnabled()) {
      $result[] = UserGroup::GROUP_ANALYSIS;
    }

    return $result;
  }

  /**
   * Retrieve all users, including group information.
   *
   * This method is born to avoid rewriting the entity model for the user groups, as that would
   * require massive changes in multiple subsystems.
   *
   * @return UserPermissions[]
   */
  public function getUserPermissions(): array
  {
    $result = [];
    foreach ($this->userGroups as $userGroup) {
      foreach ($userGroup->getUsers() as $user) {
        if (!array_key_exists($user->getId(), $result)) {
          $result[$user->getId()] = new UserPermissions($user, NULL);
        }
        $result[$user->getId()]->addPermissionFromGroup($userGroup);
      }
      foreach ($userGroup->getEmails() as $email) {
        if (!array_key_exists($email->getEmail(), $result)) {
          $result[$email->getEmail()] = new UserPermissions(NULL, $email);
        }
        $result[$email->getEmail()]->addPermissionFromGroup($userGroup);
      }
    }

    return array_values($result);
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
   * @param User|null $user
   *
   * @return bool
   */
  public function isOwner(?User $user)
  {
    if (!$user) {
      return false;
    }

    return $user->getId() === $this->owner->getId();
  }

  /**
   * Check whether the StudyArea is visible for the user
   *
   * @param User|null $user
   *
   * @return bool
   */
  public function isVisible(?User $user)
  {
    if ($this->openAccess) {
      return true;
    }

    if (!$user) {
      return false;
    }

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

  /**
   * Check whether the StudyArea is editable for the user
   *
   * @param User|null $user
   *
   * @return bool
   */
  public function isEditable(?User $user)
  {
    if (!$user) {
      return false;
    }

    return $this->isOwner($user) || $this->isUserInGroup($user, UserGroup::GROUP_EDITOR);
  }

  /**
   * Check whether the user can view the analytics of this study area
   *
   * @param User|null $user
   *
   * @return bool
   */
  public function canViewAnalytics(?User $user)
  {
    if (!$this->isAnalyticsDashboardEnabled()) {
      return false;
    }

    if (!$user) {
      return false;
    }

    return $this->isOwner($user) || $this->isUserInGroup($user, UserGroup::GROUP_ANALYSIS);
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
    foreach ($this->contributors as $contributor) {
      $check($contributor);
    }
    foreach ($this->learningOutcomes as $learningOutcome) {
      $check($learningOutcome);
    }
    foreach ($this->learningPaths as $learningPath) {
      $check($learningPath);
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

  /**
   * @param DateTime|null $frozenOn
   *
   * @return $this
   */
  public function setFrozenOn(?DateTime $frozenOn)
  {
    $this->frozenOn = $frozenOn;

    return $this;
  }

  /**
   * @return DateTime|null
   */
  public function getFrozenOn(): ?DateTime
  {
    return $this->frozenOn;
  }

  /**
   * @return bool
   */
  public function isFrozen(): bool
  {
    return $this->getFrozenOn() !== NULL;
  }

  /**
   * @return string|null
   */
  public function getPrintHeader(): ?string
  {
    return $this->printHeader;
  }

  /**
   * @param string|null $printHeader
   *
   * @return StudyArea
   */
  public function setPrintHeader(?string $printHeader): StudyArea
  {
    $this->printHeader = $printHeader;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getPrintIntroduction(): ?string
  {
    return $this->printIntroduction;
  }

  /**
   * @param string|null $printIntroduction
   *
   * @return StudyArea
   */
  public function setPrintIntroduction(?string $printIntroduction): StudyArea
  {
    $this->printIntroduction = $printIntroduction;

    return $this;
  }

  /**
   * @return bool
   */
  public function isTrackUsers(): bool
  {
    // Never enable tracking when open access is set
    return !$this->openAccess && $this->trackUsers;
  }

  /**
   * @param bool $trackUsers
   *
   * @return StudyArea
   */
  public function setTrackUsers(bool $trackUsers): StudyArea
  {
    $this->trackUsers = $trackUsers;

    return $this;
  }

  /**
   * @return StudyAreaGroup|null
   */
  public function getGroup(): ?StudyAreaGroup
  {
    return $this->group;
  }

  /**
   * @return int|null
   */
  public function getGroupId(): ?int
  {
    return $this->group ? $this->group->getId() : NULL;
  }

  /**
   * @param StudyAreaGroup|null $group
   *
   * @return StudyArea
   */
  public function setGroup(?StudyAreaGroup $group): self
  {
    $this->group = $group;

    return $this;
  }

  /**
   * @return bool
   */
  public function isOpenAccess(): bool
  {
    return $this->openAccess;
  }

  /**
   * @param bool $openAccess
   *
   * @return StudyArea
   */
  public function setOpenAccess(bool $openAccess): self
  {
    $this->openAccess = $openAccess;

    return $this;
  }

  /**
   * @return bool
   */
  public function isAnalyticsDashboardEnabled(): bool
  {
    return $this->analyticsDashboardEnabled;
  }

  /**
   * @param bool $analyticsDashboardEnabled
   *
   * @return StudyArea
   */
  public function setAnalyticsDashboardEnabled(bool $analyticsDashboardEnabled): self
  {
    $this->analyticsDashboardEnabled = $analyticsDashboardEnabled;

    return $this;
  }

  /**
   * @return bool
   */
  public function isReviewModeEnabled(): bool
  {
    return $this->reviewModeEnabled;
  }

  /**
   * @param bool $reviewModeEnabled
   *
   * @return StudyArea
   */
  public function setReviewModeEnabled(bool $reviewModeEnabled): self
  {
    $this->reviewModeEnabled = $reviewModeEnabled;

    return $this;
  }

}
