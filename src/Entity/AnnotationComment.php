<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TextAnnotation.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\AnnotationCommentRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class AnnotationComment implements IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * The user.
   *
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="annotations")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
   * @Assert\NotNull()
   */
  private ?User $user = null;

  /**
   *
   * @ORM\ManyToOne(targetEntity="Annotation", inversedBy="comments")
   * @ORM\JoinColumn(name="annotation_id", referencedColumnName="id", nullable=false)
   * @Assert\NotNull()
   */
  private ?Annotation $annotation = null;

  /**
   *
   * @ORM\Column(name="text", type="text", nullable=false)
   *
   * @Assert\NotBlank()
   * @JMSA\Expose()
   */
  private ?string $text = null;

  /**
   * @JMSA\VirtualProperty()
   * @JMSA\Expose()
   */
  public function getAuthoredTime(): DateTime
  {
    return $this->createdAt;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  /**
   * @JMSA\VirtualProperty()
   * @JMSA\Expose()
   */
  public function getUserId(): int
  {
    return $this->user->getId();
  }

  /**
   * @JMSA\VirtualProperty()
   * @JMSA\Expose()
   */
  public function getUserName(): string
  {
    return $this->user->getDisplayName();
  }

  public function setUser(?User $user): AnnotationComment
  {
    $this->user = $user;

    return $this;
  }

  public function getAnnotation(): ?Annotation
  {
    return $this->annotation;
  }

  public function setAnnotation(?Annotation $annotation): AnnotationComment
  {
    $this->annotation = $annotation;

    return $this;
  }

  public function getText(): ?string
  {
    return $this->text;
  }

  public function setText(?string $text): AnnotationComment
  {
    $this->text = $text;

    return $this;
  }
}
