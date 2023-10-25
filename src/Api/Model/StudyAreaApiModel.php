<?php

namespace App\Api\Model;

use App\Entity\LayoutConfiguration;
use App\Entity\StudyArea;
use App\Entity\StylingConfiguration;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

class StudyAreaApiModel
{
  protected function __construct(
    protected readonly int $id,
    #[Groups(['Default', 'mutate'])]
    protected readonly string $name,
    #[OA\Property(nullable: true)]
    #[Groups(['Default', 'mutate'])]
    protected readonly ?string $description,
    #[OA\Property(nullable: true)]
    protected readonly ?string $group,
    public readonly bool $dotron,
    #[OA\Property(description: 'Default Dotron layout configuration for a study area, only returned when Dotron is been enabled', type: 'object', nullable: true)]
    #[Groups(['dotron'])]
    #[Exclude(if: 'object !== null && object.dotron === false')]
    protected readonly ?int $defaultLayout,
    #[OA\Property(description: 'Default Dotron styling configuration for a study area, only returned when Dotron is been enabled', type: 'object', nullable: true)]
    #[Groups(['dotron'])]
    #[Exclude(if: 'object !== null && object.dotron === false')]
    protected readonly ?int $defaultStyling,
  ) {
  }

  public static function fromEntity(StudyArea $studyArea): self
  {
    return new self(
      $studyArea->getId(),
      $studyArea->getName(),
      $studyArea->getDescription(),
      $studyArea->getGroup()?->getName(),
      $studyArea->isDotron(),
      $studyArea->getDefaultLayoutConfiguration()?->getId(),
      $studyArea->getDefaultStylingConfiguration()?->getId(),
    );
  }

  public function getDefaultLayout(): ?int
  {
    return $this->defaultLayout ?? null;
  }

  public function getDefaultStyling(): ?int
  {
    return $this->defaultStyling ?? null;
  }

  public function mapToEntity(?StudyArea $studyArea, ?LayoutConfiguration $defaultLayoutConfiguration, ?StylingConfiguration $defaultStylingConfiguration): StudyArea
  {
    return ($studyArea ?? new StudyArea())
      ->setName($this->name ?? $studyArea?->getName() ?? '')
      ->setDescription($this->description ?? $studyArea?->getDescription() ?? '')
      ->setDefaultLayoutConfiguration($defaultLayoutConfiguration ?? $studyArea->getDefaultLayoutConfiguration() ?? null)
      ->setDefaultStylingConfiguration($defaultStylingConfiguration ?? $studyArea->getDefaultStylingConfiguration() ?? null);
  }
}
