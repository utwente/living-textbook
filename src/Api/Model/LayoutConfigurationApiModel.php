<?php

namespace App\Api\Model;

use App\Entity\LayoutConfiguration;
use Drenso\Shared\IdMap\IdMap;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

class LayoutConfigurationApiModel
{
  protected function __construct(
    protected readonly int $id,
    #[Groups(['Default', 'mutate'])]
    protected readonly string $name,
    #[Type('array<string, array>')]
    #[Groups(['Default', 'mutate'])]
    protected readonly ?array $layouts,
    #[Groups(['Default'])]
    #[Type(IdMap::class)]
    protected readonly IdMap $overrides,
  ) {
  }

  public static function fromEntity(LayoutConfiguration $layoutConfiguration): self
  {
    return new self(
      $layoutConfiguration->getId(),
      $layoutConfiguration->getName(),
      $layoutConfiguration->getLayouts(),
      new IdMap($layoutConfiguration->getOverrides()
        ->map(fn ($override) => LayoutConfigurationOverrideApiModel::fromEntity($override))->getValues()),
    );
  }

  public function mapToEntity(?LayoutConfiguration $layoutConfiguration): LayoutConfiguration
  {
    $layoutConfiguration ??= new LayoutConfiguration();

    return $layoutConfiguration
      ->setName($this->name ?? $layoutConfiguration->getName())
      ->setLayouts($this->layouts ?? $layoutConfiguration->getLayouts() ?? []);
  }
}
