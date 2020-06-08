<?php

namespace App\Naming;

use App\Entity\StudyArea;
use App\Entity\StudyAreaFieldConfiguration;
use App\Naming\Model\ResolvedConceptNames;
use App\Naming\Model\ResolvedNames;
use App\Repository\StudyAreaFieldConfigurationRepository;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The naming service is responsible for the names printed in the application.
 * They either come from the configuration, or from the the default translations
 */
class NamingService
{
  private const CACHE_TAG = 'studyarea.naming';

  /**
   * @var TagAwareAdapter
   */
  private $cache;
  /**
   * @var StudyAreaFieldConfigurationRepository
   */
  private $fieldConfigurationRepository;
  /**
   * @var TranslatorInterface
   */
  private $translator;

  /**
   * @var StudyArea
   */
  private $studyArea;

  public function __construct(
      TranslatorInterface $translator, StudyAreaFieldConfigurationRepository $fieldConfigurationRepository)
  {
    $this->translator                   = $translator;
    $this->fieldConfigurationRepository = $fieldConfigurationRepository;
    $this->cache                        = new TagAwareAdapter(new ApcuAdapter());
  }

  /**
   * Injects the current study area into this service
   *
   * @param StudyArea $studyArea
   */
  public function injectStudyArea(StudyArea $studyArea)
  {
    $this->studyArea = $studyArea;
  }

  /**
   * Get the resolved names for use anywhere.
   * If the study area is not supplied, the request area will be used.
   *
   * @param StudyArea|null $studyArea
   *
   * @return ResolvedNames
   */
  public function get(?StudyArea $studyArea = NULL): ResolvedNames
  {
    return $this->getCached($studyArea ?: $this->studyArea);
  }

  /** @noinspection PhpUnhandledExceptionInspection */
  public function clearCache()
  {
    $this->cache->invalidateTags([self::CACHE_TAG]);
  }

  /**
   * Resolve the naming for a study area.
   * This is cached, which means it must be cleared when editing the name configuration.
   *
   * @param StudyArea $studyArea
   *
   * @return ResolvedNames
   *
   * @noinspection PhpDocMissingThrowsInspection
   */
  private function getCached(StudyArea $studyArea): ResolvedNames
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    return $this->cache->get(sprintf('studyarea.%d.naming', $studyArea->getId()),
        function (ItemInterface $item) use ($studyArea) {
          $conf = $studyArea->getFieldConfiguration() ?: new StudyAreaFieldConfiguration();

          $conceptNames = new ResolvedConceptNames(
              $conf->getConceptDefinitionName() ?: $this->translator->trans('concept.definition'),
              $conf->getConceptIntroductionName() ?: $this->translator->trans('concept.introduction'),
              $conf->getConceptSynonymsName() ?: $this->translator->trans('concept.synonyms'),
              $conf->getConceptPriorKnowledgeName() ?: $this->translator->trans('concept.prior-knowledge'),
              $conf->getConceptTheoryExplanationName() ?: $this->translator->trans('concept.theory-explanation'),
              $conf->getConceptHowtoName() ?: $this->translator->trans('concept.how-to'),
              $conf->getConceptExamplesName() ?: $this->translator->trans('concept.examples'),
              $conf->getConceptSelfAssessmentName() ?: $this->translator->trans('concept.self-assessment')
          );

          $result = new ResolvedNames($conceptNames);

          $item->tag(self::CACHE_TAG);

          return $result;
        });
  }
}
