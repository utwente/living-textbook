<?php

namespace App\Entity;

use App\Repository\StylingConfigurationRelationOverrideRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: StylingConfigurationRelationOverrideRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(['relation', 'stylingConfiguration'])]
#[Gedmo\SoftDeleteable]
#[JMSA\ExclusionPolicy('all')]
class StylingConfigurationRelationOverride extends Override
{
  #[ORM\ManyToOne(inversedBy: 'stylingOverrides')]
  #[JMSA\Expose]
  /** @phpstan-ignore-next-line doctrine.associationType */
  private ConceptRelation $relation;

  #[ORM\ManyToOne(inversedBy: 'relationOverrides')]
  /** @phpstan-ignore-next-line doctrine.associationType */
  private StylingConfiguration $stylingConfiguration;

  public function __construct(
    StudyArea $studyArea,
    ConceptRelation $relation,
    StylingConfiguration $stylingConfiguration,
    ?array $override,
  ) {
    parent::__construct($studyArea, $override);
    $this->relation             = $relation;
    $this->stylingConfiguration = $stylingConfiguration;
  }

  public function getRelation(): ConceptRelation
  {
    return $this->relation;
  }

  public function getStylingConfiguration(): StylingConfiguration
  {
    return $this->stylingConfiguration;
  }
}
