<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserGroupEmail.
 *
 * @author BobV
 *
 * @ORM\Table(indexes={@ORM\Index(name="user_group_idx", columns={"user_group_id"}),
 *
 *                     @ORM\Index(name="email_idx", columns={"email"})})
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserGroupEmailRepository")
 */
class UserGroupEmail
{
  /**
   * @ORM\Id()
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\UserGroup", inversedBy="emails")
   *
   * @ORM\JoinColumn(name="user_group_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private ?UserGroup $userGroup = null;

  /**
   * @ORM\Id()
   *
   * @ORM\Column(name="email", type="string", length=180, nullable=false)
   *
   * @Assert\NotBlank()
   *
   * @Assert\Email()
   *
   * @Assert\Length(max=180)
   */
  private string $email = '';

  /** Custom sorter, based on email. */
  public static function sortOnEmail(UserGroupEmail $a, UserGroupEmail $b): int
  {
    return strcmp($a->getEmail(), $b->getEmail());
  }

  public function getUserGroup(): UserGroup
  {
    return $this->userGroup;
  }

  public function setUserGroup(UserGroup $userGroup): UserGroupEmail
  {
    $this->userGroup = $userGroup;

    return $this;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function setEmail(string $email): UserGroupEmail
  {
    $this->email = $email;

    return $this;
  }
}
