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
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Stringable;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\StudyAreaRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class StudyArea implements Stringable, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  // Access types, used to determine if a user can access the study area
  final public const ACCESS_PUBLIC  = 'public';
  final public const ACCESS_PRIVATE = 'private';
  final public const ACCESS_GROUP   = 'group';

  /**
   * @ORM\Column(name="name", type="string", length=255, nullable=false)
   * @Assert\NotBlank()
   * @Assert\Length(min=3, max=255)
   *
   * @JMSA\Expose()
   */
  private string $name = '';

  /** @ORM\Column(name="description", type="text", nullable=true) */
  private ?string $description = null;

  /**
   * @var Collection<Concept>
   *
   * @ORM\OneToMany(targetEntity="Concept", mappedBy="studyArea", cascade={"persist","remove"})
   *
   * @JMSA\Expose()
   */
  private $concepts;

  /**
   * @var Collection<UserGroup>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\UserGroup", mappedBy="studyArea", cascade={"persist","remove"})
   *
   * @JMSA\Expose()
   */
  private $userGroups;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="owner_user_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private ?User $owner = null;

  /**
   * @ORM\Column(name="access_type", type="string", length=10, nullable=false)
   *
   * @Assert\NotNull()
   * @StudyAreaAccessType()
   */
  private string $accessType = self::ACCESS_PRIVATE;

  /**
   * @var Collection<RelationType>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\RelationType", mappedBy="studyArea")
   */
  private $relationTypes;

  /**
   * @var Collection<Abbreviation>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\Abbreviation", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $abbreviations;

  /**
   * @var Collection<ExternalResource>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\ExternalResource", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $externalResources;

  /**
   * @var Collection<Contributor>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\Contributor", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $contributors;

  /**
   * @var Collection<LearningOutcome>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\LearningOutcome", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $learningOutcomes;

  /**
   * @var Collection<LearningPath>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\LearningPath", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $learningPaths;

  /**
   * @var Collection<Tag>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\Tag", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private $tags;

  /** @ORM\Column(name="frozen_on", type="datetime", nullable=true) */
  private ?DateTime $frozenOn = null;

  /**
   * @ORM\Column(name="print_header", type="string", length=100, nullable=true)
   *
   * @Assert\Length(max=100)
   */
  private ?string $printHeader = null;

  /** @ORM\Column(name="print_introduction", type="text", nullable=true) */
  private ?string $printIntroduction = null;

  /**
   * If set, user interaction will be tracked (with user opt-in).
   *
   * @ORM\Column(name="track_users", type="boolean", nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\Type("bool")
   */
  private bool $trackUsers = false;

  /**
   * Group.
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\StudyAreaGroup", inversedBy="studyAreas")
   * @ORM\JoinColumn(nullable=true)
   */
  private ?StudyAreaGroup $group = null;

  /**
   * Open access.
   *
   * @ORM\Column(type="boolean", nullable=false, options={"default": false})
   */
  private bool $openAccess = false;

  /**
   * Analytics dashboard enabled.
   *
   * @ORM\Column(type="boolean", nullable=false, options={"default": false})
   */
  private bool $analyticsDashboardEnabled = false;

  /**
   * Whether the review mode has been enabled for this study area.
   *
   * @ORM\Column(type="boolean", nullable=false, options={"default": false})
   */
  private bool $reviewModeEnabled = false;

  /**
   * Whether the API is enabled for this study area.
   *
   * @ORM\Column(type="boolean", nullable=false, options={"default": false})
   */
  private bool $apiEnabled = false;

  /**
   * The study area field names object.
   *
   * @ORM\OneToOne(targetEntity="App\Entity\StudyAreaFieldConfiguration", cascade={"all"})
   * @ORM\JoinColumn(nullable=true)
   */
  private ?StudyAreaFieldConfiguration $fieldConfiguration = null;

  /**
   * A default tag filter for the browser.
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\Tag")
   * @ORM\JoinColumn(nullable=true)
   */
  private ?Tag $defaultTagFilter = null;

  /**
   * If set the Dotron visualisation will be used.
   *
   * @ORM\Column(type="boolean")
   */
  private bool $dotron = false;

  /** @ORM\OneToOne(targetEntity="App\Entity\StylingConfiguration") */
  private ?StylingConfiguration $defaultStylingConfiguration = null;

  /**
   * @var Collection<int, StylingConfiguration>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\StylingConfiguration", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private Collection $stylingConfigurations;

  /** @ORM\OneToOne(targetEntity="App\Entity\LayoutConfiguration") */
  private ?LayoutConfiguration $defaultLayoutConfiguration = null;

  /**
   * @var Collection<int, LayoutConfiguration>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\LayoutConfiguration", mappedBy="studyArea", fetch="EXTRA_LAZY")
   */
  private Collection $layoutConfigurations;

  /** StudyArea constructor. */
  public function __construct()
  {
    $this->concepts              = new ArrayCollection();
    $this->userGroups            = new ArrayCollection();
    $this->relationTypes         = new ArrayCollection();
    $this->abbreviations         = new ArrayCollection();
    $this->externalResources     = new ArrayCollection();
    $this->contributors          = new ArrayCollection();
    $this->learningOutcomes      = new ArrayCollection();
    $this->learningPaths         = new ArrayCollection();
    $this->tags                  = new ArrayCollection();
    $this->stylingConfigurations = new ArrayCollection();
    $this->layoutConfigurations  = new ArrayCollection();
  }

  /** @Assert\Callback() */
  public function validateObject(ExecutionContextInterface $context)
  {
    if ($this->reviewModeEnabled && $this->apiEnabled) {
      $context->buildViolation('study-area.api-and-review-mode-enabled')
          ->atPath('apiEnabled')
          ->addViolation();
    }

    if ($this->dotron && !$this->apiEnabled) {
      $context->buildViolation('study-area.api-disabled-and-dotron-enabled')
          ->atPath('dotron')
          ->addViolation();
    }
  }

  /**
   * Possible access types.
   *
   * @return string[]
   */
  public static function getAccessTypes(): array
  {
    return [self::ACCESS_PUBLIC, self::ACCESS_PRIVATE, self::ACCESS_GROUP];
  }

  /**
   * Possible access types, depending on the access level.
   *
   * @return string[]
   */
  public function getAvailableAccessTypes(Security $security, EntityManagerInterface $em): array
  {
    // Get original field value
    $origObj   = $em->getUnitOfWork()->getOriginalEntityData($this);
    $prevValue = array_key_exists('accessType', $origObj) ? $origObj['accessType'] : null;

    // Get choices, remove public type when not administrator, and field has changed
    $choices = StudyArea::getAccessTypes();
    if (!$security->isGranted('ROLE_SUPER_ADMIN') && $prevValue !== self::ACCESS_PUBLIC) {
      $choices = array_filter($choices, fn ($item) => $item !== StudyArea::ACCESS_PUBLIC);
    }

    return $choices;
  }

  /** Check whether the user is in a certain or one of the groups */
  public function isUserInGroup(User $user, string $groupType = null): bool
  {
    foreach ($this->getUserGroups($groupType) as $userGroup) {
      if ($userGroup->getUsers()->contains($user)) {
        return true;
      }
    }

    return false;
  }

  /** @return Collection<UserGroup> */
  public function getUserGroups(string $groupType = null): Collection
  {
    return $groupType === null ? $this->userGroups : $this->userGroups->matching(
        Criteria::create()->where(Criteria::expr()->eq('groupType', $groupType)));
  }

  /**
   * Retrieve the available user group types.
   *
   * @return string[]
   */
  public function getAvailableUserGroupTypes(): array
  {
    if ($this->getAccessType() === StudyArea::ACCESS_PRIVATE) {
      return [];
    }

    $result = [];
    if ($this->getAccessType() !== StudyArea::ACCESS_PUBLIC) {
      $result[] = UserGroup::GROUP_VIEWER;
    }

    $result[] = UserGroup::GROUP_EDITOR;

    if ($this->isReviewModeEnabled()) {
      $result[] = UserGroup::GROUP_REVIEWER;
    }

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
        /* @phan-suppress-next-line PhanPossiblyUndeclaredVariable */
        if (!array_key_exists($user->getId(), $result)) {
          $result[$user->getId()] = new UserPermissions($user, null);
        }
        $result[$user->getId()]->addPermissionFromGroup($userGroup);
      }
      foreach ($userGroup->getEmails() as $email) {
        /* @phan-suppress-next-line PhanPossiblyUndeclaredVariable */
        if (!array_key_exists($email->getEmail(), $result)) {
          $result[$email->getEmail()] = new UserPermissions(null, $email);
        }
        $result[$email->getEmail()]->addPermissionFromGroup($userGroup);
      }
    }

    return array_values($result);
  }

  /**
   * Get the editors.
   *
   * @return User[]
   */
  public function getEditors(): array
  {
    $editorGroup = $this->getUserGroups(UserGroup::GROUP_EDITOR);
    if ($editorGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $users = array_merge(...$editorGroup->map(fn (UserGroup $userGroup) => $userGroup->getUsers()->toArray())->toArray());
    usort($users, User::sortOnDisplayName(...));

    return $users;
  }

  /**
   * Get the editors which do not have an account (yet).
   *
   * @return UserGroupEmail[]
   */
  public function getEmailEditors(): array
  {
    $editorGroup = $this->getUserGroups(UserGroup::GROUP_EDITOR);
    if ($editorGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $userGroupEmails = array_merge(...$editorGroup->map(fn (UserGroup $userGroup) => $userGroup->getEmails()->toArray())->toArray());
    usort($userGroupEmails, UserGroupEmail::sortOnEmail(...));

    return $userGroupEmails;
  }

  /**
   * Get the editors.
   *
   * @return User[]
   */
  public function getReviewers(): array
  {
    $reviewGroup = $this->getUserGroups(UserGroup::GROUP_REVIEWER);
    if ($reviewGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $users = array_merge(...$reviewGroup->map(fn (UserGroup $userGroup) => $userGroup->getUsers()->toArray())->toArray());
    usort($users, User::sortOnDisplayName(...));

    return $users;
  }

  /**
   * Get the reviewers which do not have an account (yet).
   *
   * @return UserGroupEmail[]
   */
  public function getEmailReviewers(): array
  {
    $reviewGroup = $this->getUserGroups(UserGroup::GROUP_REVIEWER);
    if ($reviewGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $userGroupEmails = array_merge(...$reviewGroup->map(fn (UserGroup $userGroup) => $userGroup->getEmails()->toArray())->toArray());
    usort($userGroupEmails, UserGroupEmail::sortOnEmail(...));

    return $userGroupEmails;
  }

  /**
   * Get the viewers.
   *
   * @return User[]
   */
  public function getViewers(): array
  {
    $viewerGroup = $this->getUserGroups(UserGroup::GROUP_VIEWER);
    if ($viewerGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $users = array_merge(...$viewerGroup->map(fn (UserGroup $userGroup) => $userGroup->getUsers()->toArray())->toArray());
    usort($users, User::sortOnDisplayName(...));

    return $users;
  }

  /**
   * Get the viewers which do not have an account (yet).
   *
   * @return UserGroupEmail[]
   */
  public function getEmailViewers(): array
  {
    $viewerGroup = $this->getUserGroups(UserGroup::GROUP_VIEWER);
    if ($viewerGroup->isEmpty()) {
      // Early return to prevent warning with array_merge
      return [];
    }

    $userGroupEmails = array_merge(...$viewerGroup->map(fn (UserGroup $userGroup) => $userGroup->getEmails()->toArray())->toArray());
    usort($userGroupEmails, UserGroupEmail::sortOnEmail(...));

    return $userGroupEmails;
  }

  /** Check whether the given user is the StudyArea owner */
  public function isOwner(?User $user): bool
  {
    if (!$user) {
      return false;
    }

    return $user->getId() === $this->owner->getId();
  }

  /** Check whether the StudyArea is visible for the user */
  public function isVisible(?User $user): bool
  {
    if ($this->openAccess) {
      return true;
    }

    if (!$user) {
      return false;
    }

    return match ($this->accessType) {
      StudyArea::ACCESS_PUBLIC  => true,
      StudyArea::ACCESS_PRIVATE => $this->isOwner($user),
      StudyArea::ACCESS_GROUP   => $this->isOwner($user) || $this->isUserInGroup($user),
      default                   => false,
    };
  }

  /** Check whether the StudyArea is editable for the user */
  public function isEditable(?User $user): bool
  {
    if (!$user) {
      return false;
    }

    return $this->isOwner($user) || $this->isUserInGroup($user, UserGroup::GROUP_EDITOR);
  }

  /** Check whether the StudyArea changes can be reviewed by the user */
  public function isReviewable(?User $user): bool
  {
    if (!$user || !$this->isReviewModeEnabled()) {
      return false;
    }

    return $this->isOwner($user) || $this->isUserInGroup($user, UserGroup::GROUP_REVIEWER);
  }

  /** Check whether the user can view the analytics of this study area */
  public function canViewAnalytics(?User $user): bool
  {
    if (!$this->isAnalyticsDashboardEnabled()) {
      return false;
    }

    if (!$user) {
      return false;
    }

    return $this->isOwner($user) || $this->isUserInGroup($user, UserGroup::GROUP_ANALYSIS);
  }

  /** @return array Array with DateTime and username */
  public function getLastEditInfo(): array
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
    foreach ($this->tags as $tag) {
      $check($tag);
    }
    foreach ($this->stylingConfigurations as $stylingConfiguration) {
      $check($stylingConfiguration);
    }
    foreach ($this->layoutConfigurations as $layoutConfiguration) {
      $check($layoutConfiguration);
    }

    // Return result
    return [$lastUpdated, $lastUpdatedBy];
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): self
  {
    $this->description = $description;

    return $this;
  }

  /** @return Collection<Concept> */
  public function getConcepts(): Collection
  {
    return $this->concepts;
  }

  public function addConcept(Concept $concept): self
  {
    // Check whether the study area is set, otherwise set it as this
    if (!$concept->getStudyArea()) {
      $concept->setStudyArea($this);
    }
    $this->concepts->add($concept);

    return $this;
  }

  public function removeConcept(Concept $concept): self
  {
    $this->concepts->removeElement($concept);

    return $this;
  }

  public function addUserGroup(UserGroup $userGroup): self
  {
    // Check whether the StudyArea is set, otherwise set it as this
    if (!$userGroup->getStudyArea()) {
      $userGroup->setStudyArea($this);
    }
    $this->userGroups->add($userGroup);

    return $this;
  }

  public function removeUserGroup(UserGroup $userGroup): self
  {
    $this->userGroups->removeElement($userGroup);

    return $this;
  }

  public function __toString(): string
  {
    return $this->getName();
  }

  public function getOwner(): ?User
  {
    return $this->owner;
  }

  public function setOwner(User $owner): self
  {
    $this->owner = $owner;

    return $this;
  }

  public function getAccessType(): string
  {
    return $this->accessType;
  }

  public function setAccessType(string $accessType): self
  {
    $this->accessType = $accessType;

    return $this;
  }

  /** @return Collection<RelationType> */
  public function getRelationTypes(): Collection
  {
    return $this->relationTypes;
  }

  public function setFrozenOn(?DateTime $frozenOn): self
  {
    $this->frozenOn = $frozenOn;

    return $this;
  }

  public function getFrozenOn(): ?DateTime
  {
    return $this->frozenOn;
  }

  public function isFrozen(): bool
  {
    return $this->getFrozenOn() !== null;
  }

  public function getPrintHeader(): ?string
  {
    return $this->printHeader;
  }

  public function setPrintHeader(?string $printHeader): self
  {
    $this->printHeader = $printHeader;

    return $this;
  }

  public function getPrintIntroduction(): ?string
  {
    return $this->printIntroduction;
  }

  public function setPrintIntroduction(?string $printIntroduction): self
  {
    $this->printIntroduction = $printIntroduction;

    return $this;
  }

  public function isTrackUsers(): bool
  {
    // Never enable tracking when open access is set
    return !$this->openAccess && $this->trackUsers;
  }

  public function setTrackUsers(bool $trackUsers): self
  {
    $this->trackUsers = $trackUsers;

    return $this;
  }

  public function getGroup(): ?StudyAreaGroup
  {
    return $this->group;
  }

  public function getGroupId(): ?int
  {
    return $this->group ? $this->group->getId() : null;
  }

  public function setGroup(?StudyAreaGroup $group): self
  {
    $this->group = $group;

    return $this;
  }

  public function isOpenAccess(): bool
  {
    return $this->openAccess;
  }

  public function setOpenAccess(bool $openAccess): self
  {
    $this->openAccess = $openAccess;

    return $this;
  }

  public function isAnalyticsDashboardEnabled(): bool
  {
    return $this->analyticsDashboardEnabled;
  }

  public function setAnalyticsDashboardEnabled(bool $analyticsDashboardEnabled): self
  {
    $this->analyticsDashboardEnabled = $analyticsDashboardEnabled;

    return $this;
  }

  public function isReviewModeEnabled(): bool
  {
    return $this->reviewModeEnabled;
  }

  public function setReviewModeEnabled(bool $reviewModeEnabled): self
  {
    $this->reviewModeEnabled = $reviewModeEnabled;

    return $this;
  }

  public function isApiEnabled(): bool
  {
    return $this->apiEnabled;
  }

  public function setApiEnabled(bool $apiEnabled): self
  {
    $this->apiEnabled = $apiEnabled;

    return $this;
  }

  public function getFieldConfiguration(): ?StudyAreaFieldConfiguration
  {
    return $this->fieldConfiguration;
  }

  public function setFieldConfiguration(?StudyAreaFieldConfiguration $fieldConfiguration): self
  {
    $this->fieldConfiguration = $fieldConfiguration;

    return $this;
  }

  public function getDefaultTagFilter(): ?Tag
  {
    return $this->defaultTagFilter;
  }

  public function setDefaultTagFilter(?Tag $defaultTagFilter): self
  {
    $this->defaultTagFilter = $defaultTagFilter;

    return $this;
  }

  public function isDotron(): bool
  {
    return $this->dotron;
  }

  public function setDotron(bool $dotron): self
  {
    $this->dotron = $dotron;

    return $this;
  }

  public function getDefaultStylingConfiguration(): ?StylingConfiguration
  {
    return $this->defaultStylingConfiguration;
  }

  public function setDefaultStylingConfiguration(?StylingConfiguration $defaultStylingConfiguration): self
  {
    $this->defaultStylingConfiguration = $defaultStylingConfiguration;

    return $this;
  }

  /** @return Collection<int, StylingConfiguration> */
  public function getStylingConfigurations(): Collection
  {
    return $this->stylingConfigurations;
  }

  public function getDefaultLayoutConfiguration(): ?LayoutConfiguration
  {
    return $this->defaultLayoutConfiguration;
  }

  public function setDefaultLayoutConfiguration(?LayoutConfiguration $defaultLayoutConfiguration): self
  {
    $this->defaultLayoutConfiguration = $defaultLayoutConfiguration;

    return $this;
  }

  /** @return Collection<int, LayoutConfiguration> */
  public function getLayoutConfigurations(): Collection
  {
    return $this->layoutConfigurations;
  }
}
