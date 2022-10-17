<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\StylingConfigurationRelationOverrideRepository",)
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity({"relation", "stylingConfiguration"})
 * @Gedmo\SoftDeleteable()
 * @JMSA\ExclusionPolicy("all")
 */
class StylingConfigurationRelationOverride extends Override
{
  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\ConceptRelation", inversedBy="stylingOverrides")
   *
   * @JMSA\Expose()
   */
  private ConceptRelation $relation;

  /** @ORM\ManyToOne(targetEntity="StylingConfiguration", inversedBy="relationOverrides") */
  private StylingConfiguration $stylingConfiguration;

  public function __construct(
      StudyArea $studyArea,
      ConceptRelation $relation,
      StylingConfiguration $stylingConfiguration,
      ?array $override
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
