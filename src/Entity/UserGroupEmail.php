<?php

namespace App\Entity;

use App\Repository\UserGroupEmailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use function strcmp;

#[ORM\Entity(repositoryClass: UserGroupEmailRepository::class)]
#[ORM\Table]
#[ORM\Index(columns: ['user_group_id'], name: 'user_group_idx')]
#[ORM\Index(columns: ['email'], name: 'email_idx')]
class UserGroupEmail
{
  #[Assert\NotNull]
  #[ORM\Id]
  #[ORM\ManyToOne(inversedBy: 'emails')]
  #[ORM\JoinColumn(name: 'user_group_id', referencedColumnName: 'id', nullable: false)]
  private ?UserGroup $userGroup = null;

  #[Assert\NotBlank]
  #[Assert\Email]
  #[Assert\Length(max: 180)]
  #[ORM\Id]
  #[ORM\Column(name: 'email', length: 180, nullable: false)]
  private string $email = '';

  /** Custom sorter, based on email. */
  public static function sortOnEmail(self $a, self $b): int
  {
    return strcmp($a->getEmail(), $b->getEmail());
  }

  public function getUserGroup(): UserGroup
  {
    return $this->userGroup;
  }

  public function setUserGroup(UserGroup $userGroup): self
  {
    $this->userGroup = $userGroup;

    return $this;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function setEmail(string $email): self
  {
    $this->email = $email;

    return $this;
  }
}
