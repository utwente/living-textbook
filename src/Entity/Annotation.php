<?php

namespace App\Entity;

use App\Controller\SearchController;
use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\SearchableInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Helper\StringHelper;
use Drenso\Shared\Interfaces\IdInterface;
use Exception;
use Gedmo\Mapping\Annotation as Gedmo;
use InvalidArgumentException;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TextAnnotation.
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="App\Repository\AnnotationRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @JMSA\ExclusionPolicy("all")
 */
class Annotation implements SearchableInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * The user.
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="annotations")
   *
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private ?User $user = null;

  /**
   * The concept.
   *
   * @ORM\ManyToOne(targetEntity="Concept")
   *
   * @ORM\JoinColumn(name="concept_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private ?Concept $concept = null;

  /**
   * Annotation text. If null, it is only a highlight.
   *
   * @ORM\Column(name="text", type="text", nullable=true)
   *
   * @JMSA\Expose()
   */
  private ?string $text = null;

  /**
   * Annotation context (section of concept).
   *
   * @ORM\Column(name="context", type="string", length=50, nullable=false)
   *
   * @Assert\NotNull()
   *
   * @Assert\NotBlank()
   *
   * @JMSA\Expose()
   */
  private string $context = '';

  /**
   * Annotation start. This is without any HTML tags!
   * If -1, the complete section is annotated.
   *
   * @ORM\Column(name="start", type="integer", nullable=false)
   *
   * @Assert\NotNull()
   *
   * @Assert\Range(min="-1")
   *
   * @JMSA\Expose()
   */
  private int $start = 0;

  /**
   * Annotation end. This is without any HTML tags!
   * If there is no selection, it means the header/complete context is annotated.
   *
   * @ORM\Column(name="end", type="integer", nullable=false)
   *
   * @Assert\NotNull()
   *
   * @Assert\Range(min="0")
   *
   * @Assert\Expression(
   *   "value !== this.getStart()",
   *   message="annotation.start-end-identical")
   *
   * @JMSA\Expose()
   */
  private int $end = 0;

  /**
   * The selected text at time of creation.
   * Should be null when the header is selected.
   *
   * @ORM\Column(name="selected_text", type="text", nullable=true)
   *
   * @Assert\Expression(
   *   "(value === null && this.getStart() === -1) || (value !== null && this.getStart() >= 0)",
   *   message="annotation.selection-incorrect")
   *
   * @JMSA\Expose()
   */
  private ?string $selectedText = null;

  /**
   * Annotation version, linked to context version to detect changes since annotation
   * This can only be null if the complete context is annotated.
   *
   * @ORM\Column(name="version", type="datetime", nullable=true)
   *
   * @JMSA\Expose()
   */
  private ?DateTime $version = null;

  /**
   * Visibility for the annotation.
   *
   * @ORM\Column(name="visibility", type="string", length=10)
   *
   * @JMSA\Expose()
   *
   * @Assert\Choice(callback="visibilityOptions")
   */
  private string $visibility;

  /**
   * @var Collection<AnnotationComment>
   *
   * @ORM\OneToMany(targetEntity="AnnotationComment", mappedBy="annotation")
   *
   * @Assert\Expression(
   *   "(this.getText() === null && this.getCommentCount() === 0) || (this.getText() !== null)",
   *   message="annotation.comments-incorrect"
   * )
   *
   * @Assert\Valid()
   *
   * @JMSA\Expose
   */
  private Collection $comments;

  /**
   * Annotation constructor.
   *
   * @throws Exception
   */
  public function __construct()
  {
    $this->version    = new DateTime();
    $this->visibility = self::privateVisibility();
    $this->comments   = new ArrayCollection();
  }

  /** Searches in the annotation on the given search, returns an array with search result metadata. */
  public function searchIn(string $search): array
  {
    // Create result array
    $results = [];

    // Search in different parts
    if ($this->getText() && stripos($this->getText(), $search) !== false) {
      $results[] = SearchController::createResult(255, 'text', ['text' => $this->getText(), 'author' => $this->getUser()->getFullName()]);
    }

    // Search in the comments
    $prio = 200;
    foreach ($this->comments as $comment) {
      if ($comment->getText() && stripos($comment->getText(), $search) !== false) {
        $results[] = SearchController::createResult($prio, 'comment', ['text' => $comment->getText(), 'author' => $comment->getUser()->getFullName()]);
      }
      $prio--;
    }

    return [
      '_data'   => $this,
      '_title'  => $this->getSelectedText(),
      'results' => $results,
    ];
  }

  /** Public visibility level. Only the annotation owner will see the annotation. */
  public static function privateVisibility(): string
  {
    return 'private';
  }

  /** Teacher visibility level. Teachers are allowed to see the annotation. */
  public static function teacherVisibility(): string
  {
    return 'teacher';
  }

  /**
   * Everybody visibility level. Everybody is not public, as
   * it should only be visible for everybody with rights in the specific study area.
   */
  public static function everybodyVisibility(): string
  {
    return 'everybody';
  }

  /** Visibility levels for an annotation. */
  public static function visibilityOptions(): array
  {
    return [
      self::privateVisibility(),
      self::teacherVisibility(),
      self::everybodyVisibility(),
    ];
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  /**
   * @JMSA\VirtualProperty()
   *
   * @JMSA\Expose()
   */
  public function getUserId(): int
  {
    return $this->user->getId();
  }

  /**
   * @JMSA\VirtualProperty()
   *
   * @JMSA\Expose()
   */
  public function getUserName(): string
  {
    return $this->user->getDisplayName();
  }

  /**
   * @JMSA\VirtualProperty()
   *
   * @JMSA\Expose()
   */
  public function getAuthoredTime(): DateTime
  {
    return $this->createdAt;
  }

  public function setUser(?User $user): Annotation
  {
    $this->user = $user;

    return $this;
  }

  public function getConcept(): ?Concept
  {
    return $this->concept;
  }

  /**
   * @JMSA\VirtualProperty("concept")
   *
   * @JMSA\Expose()
   */
  public function getConceptId(): int
  {
    return $this->concept->getId();
  }

  public function setConcept(?Concept $concept): Annotation
  {
    $this->concept = $concept;

    return $this;
  }

  public function getText(): ?string
  {
    return $this->text;
  }

  public function setText(?string $text): Annotation
  {
    $this->text = StringHelper::emptyToNull($text);

    return $this;
  }

  public function getContext(): string
  {
    return $this->context;
  }

  public function setContext(string $context): Annotation
  {
    $this->context = $context;

    return $this;
  }

  public function getStart(): int
  {
    return $this->start;
  }

  public function setStart(int $start): Annotation
  {
    $this->start = $start;

    return $this;
  }

  public function getEnd(): int
  {
    return $this->end;
  }

  public function setEnd(int $end): Annotation
  {
    $this->end = $end;

    return $this;
  }

  public function getSelectedText(): ?string
  {
    return $this->selectedText;
  }

  public function setSelectedText(?string $selectedText): Annotation
  {
    $this->selectedText = StringHelper::emptyToNull($selectedText);

    return $this;
  }

  public function getVersion(): ?DateTime
  {
    return $this->version;
  }

  public function setVersion(?DateTime $version): Annotation
  {
    $this->version = $version;

    return $this;
  }

  public function getVisibility(): string
  {
    return $this->visibility;
  }

  public function setVisibility(string $visibility): Annotation
  {
    if (!in_array($visibility, self::visibilityOptions())) {
      throw new InvalidArgumentException(sprintf('"%s" is not a valid visibility value!', $visibility));
    }

    $this->visibility = $visibility;

    return $this;
  }

  /** @return Collection<AnnotationComment> */
  public function getComments(): Collection
  {
    return $this->comments;
  }

  public function getCommentCount(): int
  {
    return $this->comments->count();
  }

  public function getCommentsFromOthersCount(): int
  {
    $annotation = $this;

    return $this->comments
      ->filter(fn (AnnotationComment $annotationComment) => $annotationComment->getUser()->getId() !== $annotation->getUserId())
      ->count();
  }
}
