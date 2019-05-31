<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Gedmo\Mapping\Annotation as Gedmo;
use InvalidArgumentException;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TextAnnotation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\AnnotationRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class Annotation
{

  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * The user
   *
   * @var User|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="annotations")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $user;

  /**
   * The concept
   *
   * @var Concept|null
   *
   * @ORM\ManyToOne(targetEntity="Concept")
   * @ORM\JoinColumn(name="concept_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $concept;

  /**
   * Annotation text. If null, it is only a highlight
   *
   * @var string|null
   *
   * @ORM\Column(name="text", type="text", nullable=true)
   *
   * @JMSA\Expose()
   */
  private $text;

  /**
   * Annotation context (section of concept)
   *
   * @var string
   *
   * @ORM\Column(name="context", type="string", length=50, nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\NotBlank()
   *
   * @JMSA\Expose()
   */
  private $context = '';

  /**
   * Annotation start. This is without any HTML tags!
   * If -1, the complete section is annotated
   *
   * @var int
   *
   * @ORM\Column(name="start", type="integer", nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\Range(min="-1")
   *
   * @JMSA\Expose()
   */
  private $start = 0;

  /**
   * Annotation end. This is without any HTML tags!
   * If there is no selection, it means the header/complete context is annotated
   *
   * @var int
   *
   * @ORM\Column(name="end", type="integer", nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\Range(min="0")
   * @Assert\Expression(
   *   "value !== this.getStart()",
   *   message="annotation.start-end-identical")
   *
   * @JMSA\Expose()
   */
  private $end = 0;

  /**
   * The selected text at time of creation.
   * Should be null when the header is selected
   *
   * @var string|null
   *
   * @ORM\Column(name="selected_text", type="text", nullable=true)
   *
   * @Assert\Expression(
   *   "(value === null && this.getStart() === -1) || (value !== null && this.getStart() >= 0)",
   *   message="annotation.selection-incorrect")
   *
   * @JMSA\Expose()
   */
  private $selectedText;

  /**
   * Annotation version, linked to context version to detect changes since annotation
   * This can only be null if the complete context is annotated.
   *
   * @var DateTime|null
   *
   * @ORM\Column(name="version", type="datetime", nullable=true)
   *
   * @JMSA\Expose()
   */
  private $version;

  /**
   * Visibility for the annotation
   *
   * @var string
   *
   * @ORM\Column(name="visibility", type="string", length=10)
   *
   * @JMSA\Expose()
   *
   * @Assert\Choice(callback="visibilityOptions")
   */
  private $visibility;

  /**
   * @var AnnotationComment[]|Collection
   *
   * @ORM\OneToMany(targetEntity="AnnotationComment", mappedBy="annotation")
   *
   * @Assert\Expression(
   *   "(this.getText() === null && this.getCommentCount() === 0) || (this.getText() !== null)",
   *   message="annotation.comments-incorrect"
   * )
   * @Assert\Valid()
   *
   * @JMSA\Expose
   */
  private $comments;

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

  /**
   * Public visibility level. Only the annotation owner will see the annotation.
   *
   * @return string
   */
  public static function privateVisibility(): string
  {
    return 'private';
  }

  /**
   * Teacher visibility level. Teachers are allowed to see the annotation.
   *
   * @return string
   */
  public static function teacherVisibility(): string
  {
    return 'teacher';
  }

  /**
   * Everybody visibility level. Everybody is not public, as
   * it should only be visible for everybody with rights in the specific study area
   *
   * @return string
   */
  public static function everybodyVisibility(): string
  {
    return 'everybody';
  }

  /**
   * Visibility levels for an annotation.
   */
  public static function visibilityOptions(): array
  {
    return [
        self::privateVisibility(),
        self::teacherVisibility(),
        self::everybodyVisibility(),
    ];
  }

  /**
   * @return User|null
   */
  public function getUser(): ?User
  {
    return $this->user;
  }

  /**
   * @return int
   *
   * @JMSA\VirtualProperty()
   * @JMSA\Expose()
   */
  public function getUserId(): int
  {
    return $this->user->getId();
  }

  /**
   * @return string
   *
   * @JMSA\VirtualProperty()
   * @JMSA\Expose()
   */
  public function getUserName(): string
  {
    return $this->user->getDisplayName();
  }

  /**
   * @return DateTime
   *
   * @JMSA\VirtualProperty()
   * @JMSA\Expose()
   */
  public function getAuthoredTime(): DateTime
  {
    return $this->createdAt;
  }

  /**
   * @param User|null $user
   *
   * @return Annotation
   */
  public function setUser(?User $user): Annotation
  {
    $this->user = $user;

    return $this;
  }

  /**
   * @return Concept|null
   */
  public function getConcept(): ?Concept
  {
    return $this->concept;
  }

  /**
   * @return int
   *
   * @JMSA\VirtualProperty("concept")
   * @JMSA\Expose()
   */
  public function getConceptId(): int
  {
    return $this->concept->getId();
  }

  /**
   * @param Concept|null $concept
   *
   * @return Annotation
   */
  public function setConcept(?Concept $concept): Annotation
  {
    $this->concept = $concept;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getText(): ?string
  {
    return $this->text;
  }

  /**
   * @param string|null $text
   *
   * @return Annotation
   */
  public function setText(?string $text): Annotation
  {
    $this->text = strlen($text) > 0 ? $text : NULL;

    return $this;
  }

  /**
   * @return string
   */
  public function getContext(): string
  {
    return $this->context;
  }

  /**
   * @param string $context
   *
   * @return Annotation
   */
  public function setContext(string $context): Annotation
  {
    $this->context = $context;

    return $this;
  }

  /**
   * @return int
   */
  public function getStart(): int
  {
    return $this->start;
  }

  /**
   * @param int $start
   *
   * @return Annotation
   */
  public function setStart(int $start): Annotation
  {
    $this->start = $start;

    return $this;
  }

  /**
   * @return int
   */
  public function getEnd(): int
  {
    return $this->end;
  }

  /**
   * @param int $end
   *
   * @return Annotation
   */
  public function setEnd(int $end): Annotation
  {
    $this->end = $end;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getSelectedText(): ?string
  {
    return $this->selectedText;
  }

  /**
   * @param string|null $selectedText
   *
   * @return Annotation
   */
  public function setSelectedText(?string $selectedText): Annotation
  {
    $this->selectedText = strlen($selectedText) > 0 ? $selectedText : NULL;

    return $this;
  }

  /**
   * @return DateTime|null
   */
  public function getVersion(): ?DateTime
  {
    return $this->version;
  }

  /**
   * @param DateTime|null $version
   *
   * @return Annotation
   */
  public function setVersion(?DateTime $version): Annotation
  {
    $this->version = $version;

    return $this;
  }

  /**
   * @return string
   */
  public function getVisibility(): string
  {
    return $this->visibility;
  }

  /**
   * @param string $visibility
   *
   * @return Annotation
   */
  public function setVisibility(string $visibility): Annotation
  {
    if (!in_array($visibility, self::visibilityOptions())) {
      throw new InvalidArgumentException(sprintf('"%s" is not a valid visibility value!', $visibility));
    }

    $this->visibility = $visibility;

    return $this;
  }

  /**
   * @return AnnotationComment[]|Collection
   */
  public function getComments()
  {
    return $this->comments;
  }

  /**
   * @return int
   */
  public function getCommentCount(): int
  {
    return count($this->comments);
  }

}
