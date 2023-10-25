<?php

namespace App\UrlUtils;

use App\Entity\StudyArea;
use App\Repository\ContributorRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use App\Repository\LearningPathRepository;
use App\Repository\StudyAreaRepository;
use App\UrlUtils\Model\CacheableUrl;
use App\UrlUtils\Model\Url;
use DateTime;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class UrlChecker
{
  private FilesystemAdapter $bad0UrlCache;

  private FilesystemAdapter $bad1UrlCache;

  private FilesystemAdapter $bad2UrlCache;

  private FilesystemAdapter $bad4UrlCache;

  private FilesystemAdapter $bad7UrlCache;

  private FilesystemAdapter $goodUrlsCache;

  private FilesystemAdapter $studyAreaCache;

  private ContributorRepository $contributorRepository;

  private ExternalResourceRepository $externalResourceRepository;

  private LearningOutcomeRepository $learningOutcomeRepository;

  private LearningPathRepository $learningPathRepository;

  private StudyAreaRepository $studyAreaRepository;

  private UrlScanner $urlScanner;

  private RouterInterface $router;

  /** UrlChecker constructor. */
  public function __construct(
    ExternalResourceRepository $externalResourceRepository, LearningOutcomeRepository $learningOutcomeRepository,
    StudyAreaRepository $studyAreaRepository, LearningPathRepository $learningPathRepository,
    ContributorRepository $contributorRepository, UrlScanner $urlScanner, RouterInterface $router)
  {
    $this->contributorRepository      = $contributorRepository;
    $this->externalResourceRepository = $externalResourceRepository;
    $this->learningOutcomeRepository  = $learningOutcomeRepository;
    $this->studyAreaRepository        = $studyAreaRepository;
    $this->learningPathRepository     = $learningPathRepository;
    $this->urlScanner                 = $urlScanner;
    $this->router                     = $router;
    $this->goodUrlsCache              = new FilesystemAdapter('app.url.good');
    $this->bad0UrlCache               = new FilesystemAdapter('app.url.bad.0');
    $this->bad1UrlCache               = new FilesystemAdapter('app.url.bad.1');
    $this->bad2UrlCache               = new FilesystemAdapter('app.url.bad.2');
    $this->bad4UrlCache               = new FilesystemAdapter('app.url.bad.4');
    $this->bad7UrlCache               = new FilesystemAdapter('app.url.bad.7');
    $this->studyAreaCache             = new FilesystemAdapter('app.studyarea');
  }

  /**
   * Check all the URLs in the whole application.
   *
   * @throws InvalidArgumentException
   *
   * @return array Returns an array with structure [studyarea_id]['bad'|'unscanned'][url]
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function checkAllUrls(bool $force = false, bool $fromCache = true): array
  {
    $studyAreas = $this->studyAreaRepository->findAll();
    $badUrls    = [];
    foreach ($studyAreas as $studyArea) {
      assert($studyArea instanceof StudyArea);
      $badUrls[$studyArea->getId()] = $this->checkStudyArea($studyArea, $force, $fromCache);
    }

    return $badUrls;
  }

  /**
   * Check the URLs used within one study area.
   *
   * @throws InvalidArgumentException
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function checkStudyArea(StudyArea $studyArea, bool $force = false, bool $fromCache = true): ?array
  {
    $cacheItem = $this->getUrlsForStudyArea($studyArea, $fromCache);
    if ($cacheItem === null || $cacheItem['urls'] === null) {
      return null;
    }
    $badUrls = $this->findBadUrls($cacheItem['urls'], $studyArea, $force, $fromCache);

    return $badUrls;
  }

  /**
   * Get all urls for a given study area, from cache if wanted.
   *
   * @throws InvalidArgumentException
   *
   * @return array|null Returns array with urls and last scanned time if it is cached or not retrieved from cache,
   *                    null if cache doesn't contain urls for this study area
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function getUrlsForStudyArea(StudyArea $studyArea, bool $fromCache = true): ?array
  {
    // Get all used URLs
    $studyAreaId = (string)$studyArea->getId();
    if ($fromCache) {
      if ($this->studyAreaCache->hasItem($studyAreaId)) {
        $cacheItem = $this->studyAreaCache->getItem($studyAreaId)->get();
      } else {
        return null;
      }
    } else {
      $cacheItem = ['urls' => null, 'lastScanned' => new DateTime()];
      // Early commit to cache, so it is clear scanning has commenced
      $this->studyAreaCache->save($this->studyAreaCache->getItem($studyAreaId)->set($cacheItem));
      $urls              = $this->scanStudyArea($studyArea);
      $cacheItem['urls'] = $urls;
      $this->studyAreaCache->save($this->studyAreaCache->getItem($studyAreaId)->set($cacheItem));
    }

    return $cacheItem;
  }

  /**
   * Find bad urls within an array of urls.
   *
   * @param Url[] $urls
   * @param bool  $force     Rescan all urls
   * @param bool  $fromCache Only show results that have been cached
   *
   * @throws InvalidArgumentException
   *
   * @return array Returns two arrays, one with unscanned urls and one with bad urls
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function findBadUrls(array $urls, StudyArea $studyArea, bool $force, bool $fromCache): array
  {
    $badUrls            = [];
    $wrongStudyAreaUrls = [];
    $unscannedUrls      = [];
    foreach ($urls as $url) {
      assert($url instanceof Url);
      if ($url->isInternal()) {
        $urlStatus = $this->checkInternalUrl($url, $studyArea);
        if ($urlStatus === false) {
          $wrongStudyAreaUrls[] = $url;
        } elseif ($urlStatus === null) {
          $badUrls[] = $url;
        }
      } else {
        $urlStatus = $this->checkUrl($url, $force, $fromCache);
        if ($urlStatus === false) {
          $badUrls[] = $url;
        } elseif ($urlStatus === null) {
          $unscannedUrls[] = $url;
        }
      }
    }

    return ['bad' => $badUrls, 'unscanned' => $unscannedUrls, 'wrongStudyArea' => $wrongStudyAreaUrls];
  }

  /**
   * Find the used URLs within one study area.
   *
   * @return Url[]|array Returns a flat array of URLs without origin
   */
  private function scanStudyArea(StudyArea $studyArea)
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
    foreach ($this->contributorRepository->findForStudyArea($studyArea) as $contributor) {
      $urls = array_merge($urls, $this->urlScanner->scanContributors($contributor));
    }
    foreach ($this->learningPathRepository->findForStudyArea($studyArea) as $learningPath) {
      $urls = array_merge($urls, $this->urlScanner->scanLearningPath($learningPath));
    }

    $router = $this->router;
    // Exclude latex URLs
    $urls = array_filter($urls, function (Url $entry) use ($router) {
      if (!$entry->isInternal()) {
        return true;
      }
      $cleanPath = strstr($entry->getPath(), '?', true) ?: $entry->getPath();
      try {
        $urlInfo = $router->match($cleanPath);
      } catch (ResourceNotFoundException) {
        return true;
      }

      return $urlInfo['_route'] !== 'app_latex_renderlatex';
    });

    return $urls;
  }

  /**
   * Check if a given Url gives a correct response, based on
   * https://stackoverflow.com/questions/15770903/check-if-links-are-broken-in-php.
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
   * Cache a URL in the given cache.
   *
   * @throws InvalidArgumentException
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
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
   * Check a URL and cache it.
   *
   * @throws InvalidArgumentException
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  private function checkAndCacheUrl(Url $url, AdapterInterface $newCache, bool $fromCache = false, string $modifyTime = '', ?AdapterInterface $oldCache = null): bool
  {
    $cacheableUrl = CacheableUrl::fromUrl($url);
    $cachekey     = $cacheableUrl->getCachekey();
    if ($oldCache !== null) {
      // Check if cache still valid
      $cachedUrl = $oldCache->getItem($cachekey)->get();
      assert($cachedUrl instanceof CacheableUrl);

      // Test whether it is expired, always return false if forced from cache
      if ($cachedUrl->getTimestamp() < (new DateTime())->modify($modifyTime) && !$fromCache) {
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

  private function checkInternalUrl(Url $url, StudyArea $studyArea): ?bool
  {
    $cleanPath = strstr($url->getPath(), '?', true) ?: $url->getPath();
    // Try to match, if that fails the URL is broken
    try {
      $urlInfo = $this->router->match($cleanPath);
      // If page is in the URL, it will always match to route _home. If that is the case, retry without page.
      if ($urlInfo['_route'] === '_home') {
        $urlInfo = $this->router->match(str_replace('/page', '', $cleanPath));
      }
    } catch (ResourceNotFoundException) {
      return null;
    }
    // Check if the URL is in the right study area
    if (array_key_exists('_studyArea', $urlInfo)) {
      return $urlInfo['_studyArea'] == $studyArea->getId();
    }

    // Not in a study area
    return true;
  }

  /**
   * @throws InvalidArgumentException
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function checkUrl(Url $url, bool $force, bool $fromCache = true): ?bool
  {
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
      return $this->checkAndCacheUrl($url, $this->bad1UrlCache, $fromCache, '-1 hour', $this->bad0UrlCache);
    }
    if ($this->bad1UrlCache->hasItem($cachekey)) {
      return $this->checkAndCacheUrl($url, $this->bad2UrlCache, $fromCache, '-1 day', $this->bad1UrlCache);
    }
    if ($this->bad2UrlCache->hasItem($cachekey)) {
      return $this->checkAndCacheUrl($url, $this->bad4UrlCache, $fromCache, '-2 days', $this->bad2UrlCache);
    }
    if ($this->bad4UrlCache->hasItem($cachekey)) {
      return $this->checkAndCacheUrl($url, $this->bad7UrlCache, $fromCache, '-4 days', $this->bad4UrlCache);
    }
    if ($this->bad7UrlCache->hasItem($cachekey)) {
      return $this->checkAndCacheUrl($url, $this->bad7UrlCache, $fromCache, '-7 days', $this->bad7UrlCache);
    }

    // Not cached, check now. return null if forced from cache
    return $fromCache ? null : $this->checkAndCacheUrl($url, $this->bad0UrlCache);
  }
}
