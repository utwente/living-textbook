<?php

namespace App\Entity;

use App\Repository\LayoutConfigurationOverrideRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: LayoutConfigurationOverrideRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(['concept', 'layoutConfiguration'])]
#[Gedmo\SoftDeleteable]
#[JMSA\ExclusionPolicy('all')]
#[ORM\Table]
class LayoutConfigurationOverride extends Override
{
  #[ORM\ManyToOne(inversedBy: 'layoutOverrides')]
  #[JMSA\Expose]
  private Concept $concept;

  #[ORM\ManyToOne(inversedBy: 'overrides')]
  private LayoutConfiguration $layoutConfiguration;

  public function __construct(
    StudyArea $studyArea,
    Concept $concept,
    LayoutConfiguration $layoutConfiguration,
    ?array $override
  ) {
    parent::__construct($studyArea, $override);
    $this->concept             = $concept;
    $this->layoutConfiguration = $layoutConfiguration;
  }
  public function getConcept(): Concept
  {
    return $this->concept;
  }

  public function getLayoutConfiguration(): LayoutConfiguration
  {
    return $this->layoutConfiguration;
  }
}
