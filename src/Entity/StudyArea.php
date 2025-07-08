<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Repository\StudyAreaRepository;
use App\Security\UserPermissions;
use App\Validator\Constraint\StudyAreaAccessType;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Override;
use Stringable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use function array_filter;
use function array_key_exists;
use function array_merge;
use function array_values;
use function usort;

#[ORM\Entity(repositoryClass: StudyAreaRepository::class)]
#[ORM\Table]
#[JMSA\ExclusionPolicy('all')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class StudyArea implements Stringable, IdInterface
{
  use Blameable;
  use IdTrait;
  use SoftDeletable;

  // Access types, used to determine if a user can access the study area
  final public const string ACCESS_PUBLIC  = 'public';
  final public const string ACCESS_PRIVATE = 'private';
  final public const string ACCESS_GROUP   = 'group';

  #[Assert\NotBlank]
  #[Assert\Length(min: 3, max: 255)]
  #[ORM\Column(name: 'name', length: 255, nullable: false)]
  #[JMSA\Expose]
  private string $name = '';

  #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
  private ?string $description = null;

  /** @var Collection<Concept> */
  #[ORM\OneToMany(mappedBy: 'studyArea', targetEntity: Concept::class, cascade: ['persist', 'remove'])]
  #[JMSA\Expose]
  private Collection $concepts;

  /**
   * @var Collection<UserGroup>&Selectable<UserGroup>
   */
  #[ORM\OneToMany(mappedBy: 'studyArea', targetEntity: UserGroup::class, cascade: ['persist', 'remove'])]
  #[JMSA\Expose]
  private Collection&Selectable $userGroups;

  #[Assert\NotNull]
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(name: 'owner_user_id', referencedColumnName: 'id', nullable: false)]
  private ?User $owner = null;

  #[Assert\NotNull]
  #[ORM\Column(name: 'access_type', length: 10, nullable: false)]
  #[StudyAreaAccessType]
  private string $accessType = self::ACCESS_PRIVATE;

  /** @var Collection<RelationType> */
  #[ORM\OneToMany(mappedBy: 'studyArea', targetEntity: RelationType::class)]
  private Collection $relationTypes;

  /** @var Collection<Abbreviation> */
  #[ORM\OneToMany(mappedBy: 'studyArea', targetEntity: Abbreviation::class, fetch: 'EXTRA_LAZY')]
  private Collection $abbreviations;

  /** @var Collection<ExternalResource> */
  #[ORM\OneToMany(mappedBy: 'studyArea', targetEntity: ExternalResource::class, fetch: 'EXTRA_LAZY')]
  private Collection $externalResources;

  /** @var Collection<Contributor> */
  #[ORM\OneToMany(mappedBy: 'studyArea', targetEntity: Contributor::class, fetch: 'EXTRA_LAZY')]
  private Collection $contributors;

  /** @var Collection<LearningOutcome> */
  #[ORM\OneToMany(mappedBy: 'studyArea', targetEntity: LearningOutcome::class, fetch: 'EXTRA_LAZY')]
  private Collection $learningOutcomes;

  /** @var Collection<LearningPath> */
  #[ORM\OneToMany(mappedBy: 'studyArea', targetEntity: LearningPath::class, fetch: 'EXTRA_LAZY')]
  private Collection $learningPaths;

  /** @var Collection<Tag> */
  #[ORM\OneToMany(mappedBy: 'studyArea', targetEntity: Tag::class, fetch: 'EXTRA_LAZY')]
  private Collection $tags;

  #[ORM\Column(name: 'frozen_on', nullable: true)]
  private ?DateTime $frozenOn = null;

  #[Assert\Length(max: 100)]
  #[ORM\Column(name: 'print_header', length: 100, nullable: true)]
  private ?string $printHeader = null;

  #[ORM\Column(name: 'print_introduction', type: Types::TEXT, nullable: true)]
  private ?string $printIntroduction = null;

  /** If set, user interaction will be tracked (with user opt-in). */
  #[Assert\NotNull]
  #[ORM\Column(name: 'track_users', nullable: false)]
  private bool $trackUsers = false;

  /** Group. */
  #[ORM\ManyToOne(inversedBy: 'studyAreas')]
  #[ORM\JoinColumn(nullable: true)]
  private ?StudyAreaGroup $group = null;

  /** Open access. */
  #[ORM\Column(options: ['default' => false])]
  private bool $openAccess = false;

  /** Analytics dashboard enabled. */
  #[ORM\Column(options: ['default' => false])]
  private bool $analyticsDashboardEnabled = false;

  /** Whether the review mode has been enabled for this study area. */
  #[ORM\Column(options: ['default' => false])]
  private bool $reviewModeEnabled = false;

  /** Whether the API is enabled for this study area. */
  #[ORM\Column(options: ['default' => false])]
  private bool $apiEnabled = false;

  /** The study area field names object. */
  #[ORM\OneToOne(cascade: ['all'])]
  #[ORM\JoinColumn]
  private ?StudyAreaFieldConfiguration $fieldConfiguration = null;

  /** A default tag filter for the browser. */
  #[ORM\ManyToOne]
  #[ORM\JoinColumn]
  private ?Tag $defaultTagFilter = null;

  /** Map size to use in the concept browser renderer. */
  #[ORM\Column(options: ['default' => 3000])]
  private int $mapWidth = 3000;

  #[ORM\Column(options: ['default' => 2000])]
  private int $mapHeight = 2000;

  /** If set the Dotron visualisation will be used. */
  #[ORM\Column]
  private bool $dotron = false;

  #[ORM\Column(nullable: true)]
  private ?array $dotronConfig = null;

  /** @var Collection<int, StylingConfiguration> */
  #[ORM\OneToMany(mappedBy: 'studyArea', targetEntity: StylingConfiguration::class, fetch: 'EXTRA_LAZY')]
  private Collection $stylingConfigurations;

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
  }

  #[Assert\Callback]
  public function validateObject(ExecutionContextInterface $context): void
  {
    if ($this->reviewModeEnabled && $this->apiEnabled) {
      $context->buildViolation('study-area.api-and-review-mode-enabled')
        ->atPath('apiEnabled')
        ->addViolation();
    }

    if (!$this->dotron) {
      $context->getValidator()->inContext($context)
        ->atPath('mapWidth')
        ->validate($this->mapWidth, [new Assert\Range(min: 500, max: 15000)])
        ->atPath('mapHeight')
        ->validate($this->mapHeight, [new Assert\Range(min: 500, max: 10000)]);
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
    $choices = self::getAccessTypes();
    if (!$security->isGranted('ROLE_SUPER_ADMIN') && $prevValue !== self::ACCESS_PUBLIC) {
      $choices = array_filter($choices, fn ($item) => $item !== StudyArea::ACCESS_PUBLIC);
    }

    return $choices;
  }

  /** Check whether the user is in a certain or one of the groups */
  public function isUserInGroup(User $user, ?string $groupType = null): bool
  {
    foreach ($this->getUserGroups($groupType) as $userGroup) {
      if ($userGroup->getUsers()->contains($user)) {
        return true;
      }
    }

    return false;
  }

  /** @return ReadableCollection<UserGroup>&Selectable<UserGroup> */
  public function getUserGroups(?string $groupType = null): ReadableCollection&Selectable
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
    if ($this->getAccessType() === self::ACCESS_PRIVATE) {
      return [];
    }

    $result = [];
    if ($this->getAccessType() !== self::ACCESS_PUBLIC) {
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
        if (!array_key_exists($user->getId(), $result)) {
          $result[$user->getId()] = new UserPermissions($user, null);
        }
        $result[$user->getId()]->addPermissionFromGroup($userGroup);
      }
      foreach ($userGroup->getEmails() as $email) {
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
      self::ACCESS_PUBLIC  => true,
      self::ACCESS_PRIVATE => $this->isOwner($user),
      self::ACCESS_GROUP   => $this->isOwner($user) || $this->isUserInGroup($user),
      default              => false,
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

  #[Override]
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

  public function getMapWidth(): int
  {
    return $this->mapWidth;
  }

  public function setMapWidth(int $mapWidth): void
  {
    $this->mapWidth = $mapWidth;
  }

  public function getMapHeight(): int
  {
    return $this->mapHeight;
  }

  public function setMapHeight(int $mapHeight): void
  {
    $this->mapHeight = $mapHeight;
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

  public function getDotronConfig(): ?array
  {
    return $this->dotronConfig;
  }

  public function setDotronConfig(?array $dotronConfig): self
  {
    $this->dotronConfig = $dotronConfig;

    return $this;
  }

  /** @return Collection<int, StylingConfiguration> */
  public function getStylingConfigurations(): Collection
  {
    return $this->stylingConfigurations;
  }
}
