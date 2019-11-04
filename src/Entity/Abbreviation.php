<?php

namespace App\Entity;

use App\Controller\SearchController;
use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\ReviewableInterface;
use App\Entity\Contracts\SearchableInterface;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Entity\Traits\ReviewableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Abbreviation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\AbbreviationRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class Abbreviation implements SearchableInterface, StudyAreaFilteredInterface, ReviewableInterface
{

  use IdTrait;
  use Blameable;
  use SoftDeletable;
  use ReviewableTrait;

  /**
   * @var StudyArea|null
   *
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="abbreviations")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * @var string
   *
   * @ORM\Column(name="abbreviation", length=25, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=1, max=25)
   *
   * @JMSA\Expose()
   * @JMSA\Groups({"Default", "review_change"})
   * @JMSA\Type("string")
   */
  private $abbreviation;

  /**
   * @var string
   * @ORM\Column(name="meaning", length=255, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=1, max=255)
   *
   * @JMSA\Expose()
   * @JMSA\Groups({"Default", "review_change"})
   * @JMSA\Type("string")
   */
  private $meaning;

  /**
   * Abbreviation constructor.
   */
  public function __construct()
  {
    $this->abbreviation = '';
    $this->meaning      = '';
  }

  /**
   * Searches in the abbreviation on the given search, returns an array with search result metadata
   *
   * @param string $search
   *
   * @return array
   */
  public function searchIn(string $search): array
  {
    // Create result array
    $results = [];

    // Search in different parts
    if (stripos($this->getAbbreviation(), $search) !== false) {
      $results[] = SearchController::createResult(255, 'abbreviation', $this->getAbbreviation());
    }

    if (stripos($this->getMeaning(), $search) !== false) {
      $results[] = SearchController::createResult(200, 'meaning', $this->getMeaning());
    }

    return [
        '_data'   => $this,
        '_title'  => $this->getAbbreviation(),
        'results' => $results,
    ];
  }

  public function getReviewTitle(): string
  {
    return $this->getAbbreviation();
  }

  public function getReviewFieldNames(): array
  {
    return [
        'abbreviation',
        'meaning',
    ];
  }

  public function getReviewIdFieldNames(): array
  {
    return [];
  }

  /**
   * @return StudyArea|null
   */
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return Abbreviation
   */
  public function setStudyArea(StudyArea $studyArea): Abbreviation
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  /**
   * @return string
   */
  public function getAbbreviation(): string
  {
    return $this->abbreviation;
  }

  /**
   * @param string $abbreviation
   *
   * @return Abbreviation
   */
  public function setAbbreviation(string $abbreviation): Abbreviation
  {
    $this->abbreviation = trim($abbreviation);

    return $this;
  }

  /**
   * @return string
   */
  public function getMeaning(): string
  {
    return $this->meaning;
  }

  /**
   * @param string $meaning
   *
   * @return Abbreviation
   */
  public function setMeaning(string $meaning): Abbreviation
  {
    $this->meaning = trim($meaning);

    return $this;
  }
}
