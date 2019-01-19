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
   * Check all the URLs in the whole application
   *
   * @param bool $force
   *
   * @return array
   * Returns an array with structure [studyarea_id][(s|c|l|e)item_id][url] to maintain origin
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
   * Check the URLs used within one study area
   *
   * @param StudyArea $studyArea
   * @param bool      $force
   *
   * @return array
   * Returns an array with structure [(s|c|l|e)item_id[url] to maintain origin
   */
  public function checkStudyArea(StudyArea $studyArea, $force = false): array
  {
    // Get all used URLs
    $urls    = $this->getUrlsForStudyArea($studyArea);
    $badUrls = [];
    foreach ($urls as $id => $subUrls) {
      $badUrls[$id] = $this->findBadUrls($subUrls, $studyArea, $force);
    }

    return $badUrls;
  }

  /**
   * Check the URLs used within one study area
   *
   * @param StudyArea $studyArea
   * @param bool      $force
   *
   * @return Url[]
   * Returns a flat array of URLs without origin
   */
  public function checkStudyAreaFlat(StudyArea $studyArea, $force = false): array
  {
    // Get all used URLs
    $urls = $this->getUrlsForStudyAreaFlat($studyArea);

    return $this->findBadUrls($urls, $studyArea, $force);
  }

  /**
   * Find bad urls within an array of urls
   *
   * @param Url[]     $urls
   * @param StudyArea $studyArea
   * @param           $force
   *
   * @return Url[]
   */
  public function findBadUrls($urls, StudyArea $studyArea, $force): array
  {
    $badUrls = [];
    foreach ($urls as $url) {
      assert($url instanceof Url);
      if (!$this->checkUrl($url, $studyArea, $force)) $badUrls[] = $url;
    }

    return $badUrls;
  }

  /**
   * Find the used URLs within one study area
   *
   * @param StudyArea $studyArea
   *
   * @return array
   * Returns an array with structure [(s|c|l|e)item_id][url] to maintain origin
   */
  public function getUrlsForStudyArea(StudyArea $studyArea)
  {
    $urls                            = [];
    $urls['s' . $studyArea->getId()] = $this->urlScanner->scanStudyArea($studyArea);
    foreach ($studyArea->getConcepts() as $concept) {
      $urls['c' . $concept->getId()] = $this->urlScanner->scanConcept($concept);
    }
    foreach ($this->learningOutcomeRepository->findForStudyArea($studyArea) as $learningOutcome) {
      $urls['l' . $learningOutcome->getId()] = $this->urlScanner->scanLearningOutcome($learningOutcome);
    }
    foreach ($this->externalResourceRepository->findForStudyArea($studyArea) as $externalResource) {
      $urls['e' . $externalResource->getId()] = $this->urlScanner->scanExternalResource($externalResource);
    }

    return $urls;
  }

  /**
   * Find the used URLs within one study area
   *
   * @param StudyArea $studyArea
   *
   * @return Url[]|array
   * Returns a flat array of URLs without origin
   */
  public function getUrlsForStudyAreaFlat(StudyArea $studyArea)
  {
    $urls = $this->urlScanner->scanStudyArea($studyArea);
    foreach ($studyArea->getConcepts() as $concept) {
      $urls = array_merge($urls, $this->urlScanner->scanConcept($concept));
    }
    foreach ($this->learningOutcomeRepository->findForStudyArea($studyArea) as $learningOutcome) {
      $urls = array_merge($urls, $this->urlScanner->scanLearningOutcome($learningOutcome));
    }
    foreach ($this->externalResourceRepository->findForStudyArea($studyArea) as $externalResource) {
      $urls = array_merge($urls, $this->urlScanner->scanExternalResource($externalResource));
    }

    return $urls;
  }

  /**
   * Check if a given Url gives a correct response, based on
   * https://stackoverflow.com/questions/15770903/check-if-links-are-broken-in-php
   *
   * @param Url $url
   *
   * @return bool
   */
  private function _checkUrl(Url $url): bool
  {
    $ch = curl_init();
    // Set URL
    curl_setopt($ch, CURLOPT_URL, $url->getUrl());
    // Get headers
    curl_setopt($ch, CURLOPT_HEADER, true);
    // Mute output of cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Follow redirects, so checking for status 200 will also check if a redirected URL doesn't function anymore
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // Set host header to lowercase host part of URL, to fix some issues with server not understanding uppercase hosts
    curl_setopt($ch, CURLOPT_HTTPHEADER, [sprintf('Host: %s', strtolower($url->getHost()))]);
    curl_exec($ch);
    // Get headers
    $headers = curl_getinfo($ch);
    curl_close($ch);

    // Verify HTTP code
    return $headers['http_code'] === 200;
  }

  /**
   * Cache a URL in the given cache
   *
   * @param CacheableUrl     $cacheableUrl
   * @param AdapterInterface $cache
   * @param                  $expiry
   *
   * @throws \Psr\Cache\InvalidArgumentException
   */
  private function cacheUrl(CacheableUrl $cacheableUrl, AdapterInterface $cache, $expiry): void
  {
    $cache->save(
        $cache->getItem($cacheableUrl->getCachekey())
            ->set($cacheableUrl)
            ->expiresAfter($expiry)
    );
  }

  /**
   * Check a URL and cache it
   *
   * @param Url                   $url
   * @param AdapterInterface      $newCache
   * @param string                $modifyTime
   * @param AdapterInterface|null $oldCache
   *
   * @return bool
   */
  private function checkAndCacheUrl(Url $url, AdapterInterface $newCache, string $modifyTime = '', ?AdapterInterface $oldCache = NULL): bool
  {
    $cacheableUrl = CacheableUrl::fromUrl($url);
    $cachekey     = $cacheableUrl->getCachekey();
    if ($oldCache !== NULL) {
      // Check if cache still valid
      $cachedUrl = $oldCache->getItem($cachekey)->get();
      assert($cachedUrl instanceof CacheableUrl);

      // Test whether it is expired
      if ($cachedUrl->getTimestamp() < (new \DateTime())->modify($modifyTime)) {
        $oldCache->deleteItem($cachekey);
      } else {
        return false;
      }
    }
    // Recheck
    if ($this->_checkUrl($url)) {
      // Good items expire after 7 days
      $this->cacheUrl($cacheableUrl, $this->goodUrlsCache, 7 * 24 * 60 * 60);

      return true;
    } else {
      // Bad items expire after 14 days, although they should be deleted if they're no longer valid
      $this->cacheUrl($cacheableUrl, $newCache, 14 * 24 * 60 * 60);

      return false;
    }
  }

  /**
   * @param Url       $url
   * @param StudyArea $studyArea
   *
   * @return bool
   */
  private function checkInternalUrl(Url $url, StudyArea $studyArea): bool
  {
    $urlParts = explode('/', $url->getPath());
    // Check if uploaded to current study area
    if ($urlParts[1] === 'uploads') {
      return $urlParts[2] === 'studyarea' ? $urlParts[3] == $studyArea->getId() : true;
    } else if (is_numeric($studyAreaId = $urlParts[1]) || ($urlParts[1] === 'page' && is_numeric($studyAreaId = $urlParts[2]))) {
      // No upload, check study area
      return $studyAreaId == $studyArea->getId();
    }

    // Not in a study area
    return true;
  }

  /**
   * @param Url       $url
   * @param StudyArea $studyArea
   * @param bool      $force
   *
   * @return bool
   */
  public function checkUrl(Url $url, StudyArea $studyArea, bool $force): bool
  {
    // Check internal URLs for the right study area
    if ($url->isInternal()) {
      return $this->checkInternalUrl($url, $studyArea);
    }
    // Force recheck, don't use the cache
    if ($force) {
      return $this->checkAndCacheUrl($url, $this->bad0UrlCache);
    }
    $cachekey = CacheableUrl::fromUrl($url)->getCachekey();
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