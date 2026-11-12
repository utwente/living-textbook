<?php

namespace App\Data\Import\Providers;

use App\Data\Import\ImportProviderInterface;
use App\Entity\Concept;
use App\Entity\Skills\SkillSetEnum;
use App\Entity\StudyArea;
use App\Exception\DataImportException;
use App\Repository\ConceptRepository;
use App\Skills\SkillAccessor;
use Symfony\Component\DependencyInjection\Attribute\Lazy;

use function array_is_list;
use function array_key_exists;

#[Lazy]
final readonly class SkillImportProvider implements ImportProviderInterface
{
  public function __construct(
    private ConceptRepository $conceptRepository,
    private SkillAccessor $skillAccessor,
  ) {
  }

  public static function getName(): string
  {
    return 'skills';
  }

  public function isAvailable(StudyArea $studyArea): bool
  {
    return !$studyArea->isDeprecatedSkills();
  }

  public function import(StudyArea $studyArea, array $jsonData): void
  {
    // Check structure
    if (!array_is_list($jsonData)) {
      throw new DataImportException('Skill data needs to be a list.');
    }

    foreach ($jsonData as $skillItem) {
      // We need the skill data
      if (!array_key_exists('skills', $skillItem)) {
        throw new DataImportException('Skill item needs to contain "skills" property.');
      }

      if (!array_key_exists('concept_id', $skillItem) && !array_key_exists('concept', $skillItem)) {
        throw new DataImportException('Skill item needs to contain either the "concept_id" or "concept" property.');
      }

      // We need either concept id or name
      $concept = null;
      if (array_key_exists('concept_id', $skillItem)) {
        $concept = $this->conceptRepository->findOneBy(['id' => $skillItem['concept_id'], 'studyArea' => $studyArea]);
      }

      // If not found with concept id, try with concept name if given
      if (!$concept && array_key_exists('concept', $skillItem)) {
        $concept = $this->conceptRepository->findOneBy(['name' => $skillItem['concept'], 'studyArea' => $studyArea]);
      }

      if (!$concept) {
        // Graceful skip
        continue;
      }

      // Now we can import the skill information. We do this gracefully
      self::importSkillBlock($this->skillAccessor, $concept, $skillItem['skills']);
    }
  }

  /** @param array<string, mixed> $skillData */
  public static function importSkillBlock(SkillAccessor $skillAccessor, Concept $concept, array $skillData): void
  {
    foreach (SkillSetEnum::cases() as $skillSet) {
      if (!$skillSetValue = $skillData[$skillAccessor->propertyName($skillSet)] ?? null) {
        continue;
      }

      foreach ($skillSet->getSkills() as $skill) {
        if (!$value = $skillSetValue[$skillAccessor->propertyName($skill)] ?? null) {
          continue;
        }

        $skillAccessor->setValue($concept->getSkills(), $skill, $value);
      }
    }
  }
}
