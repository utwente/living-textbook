<?php

namespace App\UrlUtils;

use App\Entity\StudyArea;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use App\Repository\StudyAreaRepository;
use App\UrlUtils\Model\CacheableUrl;
use App\UrlUtils\Model\Url;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class UrlChecker
{

  /**
   * @var FilesystemAdapter
   */
  private $bad0UrlCache;

  /**
   * @var FilesystemAdapter
   */
  private $bad1UrlCache;

  /**
   * @var FilesystemAdapter
   */
  private $bad2UrlCache;

  /**
   * @var FilesystemAdapter
   */
  private $bad4UrlCache;

  /**
   * @var FilesystemAdapter
   */
  private $bad7UrlCache;

  /**
   * @var FilesystemAdapter
   */
  private $goodUrlsCache;

  /**
   * @var ExternalResourceRepository
   */
  private $externalResourceRepository;

  /**
   * @var LearningOutcomeRepository
   */
  private $learningOutcomeRepository;

  /**
   * @var StudyAreaRepository
   */
  private $studyAreaRepository;

  /**
   * @var UrlScanner
   */
  private $urlScanner;

  /**
   * UrlChecker constructor.
   *
   * @param ExternalResourceRepository $externalResourceRepository
   * @param LearningOutcomeRepository  $learningOutcomeRepository
   * @param StudyAreaRepository        $studyAreaRepository
   * @param UrlScanner                 $urlScanner
   */
  public function __construct(ExternalResourceRepository $externalResourceRepository, LearningOutcomeRepository $learningOutcomeRepository, StudyAreaRepository $studyAreaRepository, UrlScanner $urlScanner)
  {
    $this->externalResourceRepository = $externalResourceRepository;
    $this->learningOutcomeRepository  = $learningOutcomeRepository;
    $this->studyAreaRepository        = $studyAreaRepository;
    $this->urlScanner                 = $urlScanner;
    $this->goodUrlsCache              = new FilesystemAdapter('app.url.good');
    $this->bad0UrlCache               = new FilesystemAdapter('app.url.bad.0');
    $this->bad1UrlCache               = new FilesystemAdapter('app.url.bad.1');
    $this->bad2UrlCache               = new FilesystemAdapter('app.url.bad.2');
    $this->bad4UrlCache               = new FilesystemAdapter('app.url.bad.4');
    $this->bad7UrlCache               = new FilesystemAdapter('app.url.bad.7');
  }

  /**
   * @param bool $force
   *
   * @return array
   */
  public function checkAllUrls($force = false): array
  {
    $studyAreas = $this->studyAreaRepository->findAll();
    $badUrls    = [];
    foreach ($studyAreas as $studyArea) {
      assert($studyArea instanceof StudyArea);
      $badUrls[$studyArea->getId()] = $this->checkStudyArea($studyArea, $force);
    }

    return $badUrls;

  }

  /**
   * @param StudyArea $studyArea
   * @param bool      $force
   *
   * @return array
   */
  public function checkStudyArea(StudyArea $studyArea, $force = false): array
  {
    // Get all used URLs
    $urls[$studyArea->getName()] = $this->urlScanner->scanStudyArea($studyArea);
    foreach ($studyArea->getConcepts() as $concept) {
      $urls[$concept->getName()] = $this->urlScanner->scanConcept($concept);
    }
    foreach ($this->learningOutcomeRepository->findForStudyArea($studyArea) as $learningOutcome) {
      $urls[$learningOutcome->getName()] = $this->urlScanner->scanLearningOutcome($learningOutcome);
    }
    foreach ($this->externalResourceRepository->findForStudyArea($studyArea) as $externalResource) {
      $urls[$externalResource->getTitle()] = $this->urlScanner->scanExternalResource($externalResource);
    }
    $badUrls = [];
    foreach ($urls as $name => $url) {
      foreach ($url as $urlObject) {
        assert($urlObject instanceof Url);
        if (!$this->checkUrl($urlObject, $force)) $badUrls[$name][] = $urlObject;
      }
    }

    return $badUrls;
  }

  /**
   *
   * @param Url $url
   *
   * @return bool
   */
  private function _checkUrl(Url $url): bool
  {
    if ($url->isInternal()) {//TODO check internal URL
      return true;
    } else {
      // get_headers will follow redirects, so checking for status 200 is sufficient
      $headers       = @get_headers($url->getUrl());
      $stringHeaders = (is_array($headers)) ? implode("\n ", $headers) : $headers;

      return (bool)preg_match('(HTTP/.*\s+200\s)', $stringHeaders);
    }
  }

  /**
   * @param Url                   $url
   * @param AdapterInterface      $newCache
   * @param string                $modifyTime
   * @param AdapterInterface|null $oldCache
   *
   * @return bool
   */
  private function checkAndCacheUrl(Url $url, AdapterInterface $newCache, string $modifyTime = '', ?AdapterInterface $oldCache = NULL): bool
  {
    $cacheableUrl = $url->toCacheable();
    $cachekey     = $cacheableUrl->getCachekey();
    if ($oldCache !== NULL) {
      // Check if cache still valid
      $cachedUrl = $oldCache->getItem($cachekey)->get();
      assert($cachedUrl instanceof CacheableUrl);

      // Test if it is expired
      if ($cachedUrl->getTimestamp() < (new \DateTime())->modify($modifyTime)) {
        $oldCache->deleteItem($cachekey);
      } else {
        return false;
      }
    }
    // Recheck
    if ($this->_checkUrl($url)) {
      $newItem = $this->goodUrlsCache->getItem($cachekey);
      $newItem->set($cacheableUrl);
      // Good items expire after 7 days
      $newItem->expiresAfter(7 * 24 * 60 * 60);
      $this->goodUrlsCache->save($newItem);

      return true;
    } else {
      $newItem = $newCache->getItem($cachekey);
      $newItem->set($cacheableUrl);
      // Bad items expire after 14 days, although they should be deleted if they're no longer valid
      $newItem->expiresAfter(14 * 24 * 60 * 60);
      $newCache->save($newItem);

      return false;
    }
  }

  /**
   * @param Url  $url
   * @param bool $force
   *
   * @return bool
   */
  private function checkUrl(Url $url, bool $force): bool
  {
    // Force recheck, don't use the cache
    if ($force) {
      return $this->checkAndCacheUrl($url, $this->bad0UrlCache);
    }
    $cachekey = $url->toCacheable()->getCachekey();
    // Don't recheck if the url is still cached
    if ($this->goodUrlsCache->hasItem($cachekey)) {
      return true;
    }
    // Check if it exists in the bad URL caches
    if ($this->bad0UrlCache->hasItem($cachekey)) {
      return $this->checkAndCacheUrl($url, $this->bad1UrlCache, '-1 hour', $this->bad0UrlCache);
    }
    if ($this->bad1UrlCache->hasItem($cachekey)) {
      return $this->checkAndCacheUrl($url, $this->bad2UrlCache, '-1 day', $this->bad1UrlCache);
    }
    if ($this->bad2UrlCache->hasItem($cachekey)) {
      return $this->checkAndCacheUrl($url, $this->bad4UrlCache, '-2 days', $this->bad2UrlCache);
    }
    if ($this->bad4UrlCache->hasItem($cachekey)) {
      return $this->checkAndCacheUrl($url, $this->bad7UrlCache, '-4 days', $this->bad4UrlCache);
    }
    if ($this->bad7UrlCache->hasItem($cachekey)) {
      return $this->checkAndCacheUrl($url, $this->bad7UrlCache, '-7 days', $this->bad7UrlCache);
    }

    // Not cached, check now
    return $this->checkAndCacheUrl($url, $this->bad0UrlCache);

  }
}