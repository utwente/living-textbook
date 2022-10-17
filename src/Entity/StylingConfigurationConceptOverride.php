<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\StylingConfigurationConceptOverrideRepository",)
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity({"concept", "stylingConfiguration"})
 * @Gedmo\SoftDeleteable()
 * @JMSA\ExclusionPolicy("all")
 */
class StylingConfigurationConceptOverride extends Override
{
  /**
   * @ORM\ManyToOne(targetEntity="Concept", inversedBy="stylingOverrides")
   *
   * @JMSA\Expose()
   */
  private Concept $concept;

  /** @ORM\ManyToOne(targetEntity="StylingConfiguration", inversedBy="conceptOverrides") */
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
