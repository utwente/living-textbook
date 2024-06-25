<?php

namespace App\Entity;

use App\Repository\StylingConfigurationConceptOverrideRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: StylingConfigurationConceptOverrideRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(['concept', 'stylingConfiguration'])]
#[Gedmo\SoftDeleteable]
#[JMSA\ExclusionPolicy('all')]
#[ORM\Table]
class StylingConfigurationConceptOverride extends Override
{
  #[ORM\ManyToOne(inversedBy: 'stylingOverrides')]
  #[JMSA\Expose]
  private Concept $concept;

  #[ORM\ManyToOne(inversedBy: 'conceptOverrides')]
  private StylingConfiguration $stylingConfiguration;

  public function __construct(
    StudyArea $studyArea,
    Concept $concept,
    StylingConfiguration $stylingConfiguration,
    ?array $override
  ) {
    parent::__construct($studyArea, $override);
    $this->concept              = $concept;
    $this->stylingConfiguration = $stylingConfiguration;
  }

  public function getConcept(): Concept
  {
    return $this->concept;
  }

  public function getStylingConfiguration(): StylingConfiguration
  {
    return $this->stylingConfiguration;
  }
}
