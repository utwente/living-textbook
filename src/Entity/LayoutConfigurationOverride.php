<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="App\Repository\LayoutConfigurationOverrideRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity({"concept", "layoutConfiguration"})
 *
 * @Gedmo\SoftDeleteable()
 *
 * @JMSA\ExclusionPolicy("all")
 */
class LayoutConfigurationOverride extends Override
{
  /**
   * @ORM\ManyToOne(targetEntity="Concept", inversedBy="layoutOverrides")
   *
   * @JMSA\Expose()
   */
  private Concept $concept;

  /** @ORM\ManyToOne(targetEntity="LayoutConfiguration", inversedBy="overrides") */
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
