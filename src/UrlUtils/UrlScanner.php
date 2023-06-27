<?php

namespace App\UrlUtils;

use App\Entity\Concept;
use App\Entity\Contributor;
use App\Entity\ExternalResource;
use App\Entity\LearningOutcome;
use App\Entity\LearningPath;
use App\Entity\StudyArea;
use App\UrlUtils\Model\Url;
use App\UrlUtils\Model\UrlContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class UrlScanner.
 *
 * The URL scanner class is responsible for scanning texts in order to isolate a list of URLS
 */
class UrlScanner
{
  /**
   * URL pattern
   * Stolen from https://gist.github.com/gruber/8891611.
   *
   * @var string
   */
  private $urlPattern = '#(?xi)(?:(?:src|href)\s*=\s*["\']?\s*)?\b((?:https?:(?:\/{1,3}|[a-z0-9%])|[a-z0-9.\-]+[.](?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\/)(?:[^\s()<>{}\[\]]+|\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\))+(?:\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’])|(?:(?<!@)[a-z0-9]+(?:[.\-][a-z0-9]+)*[.](?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\b\/?(?!@)))#';

  /**
   * Pattern to determine whether or not the url is internal.
   *
   * @var string
   */
  private $internalPattern;

  /**
   * URL pattern to find relative linked urls.
   *
   * @var string
   */
  private $internalLinkPattern = '#(?xi)(?:src|href)\s*=\s*["\'](\/.+?)["\']#';

  /** UrlScanner constructor. */
  public function __construct(RouterInterface $router)
  {
    $this->internalPattern = '#(?xi)\b((?:https?:(?:\/{1,3}' . preg_quote($router->getContext()->getHost()) . ')))#';
  }

  /**
   * Scan a study area for a inline links.
   *
   * @return Url[]
   */
  public function scanStudyArea(StudyArea $studyArea): array
  {
    return $this->scanText($studyArea->getDescription(), new UrlContext(StudyArea::class, $studyArea->getId(), 'description'));
  }

  /**
   * Scan a concept for inline links.
   *
   * @return Url[]
   */
  public function scanConcept(Concept $concept): array
  {
    $id = $concept->getId();

    return array_values(array_unique(array_merge(
        $this->scanText($concept->getIntroduction()->getText(), new UrlContext(Concept::class, $id, 'introduction')),
        $this->scanText($concept->getTheoryExplanation()->getText(), new UrlContext(Concept::class, $id, 'theoryExplanation')),
        $this->scanText($concept->getHowTo()->getText(), new UrlContext(Concept::class, $id, 'howTo')),
        $this->scanText($concept->getExamples()->getText(), new UrlContext(Concept::class, $id, 'examples')),
        $this->scanText($concept->getSelfAssessment()->getText(), new UrlContext(Concept::class, $id, 'selfAssessment')),
        $this->scanText($concept->getAdditionalResources()->getText(), new UrlContext(Concept::class, $id, 'additionalResources'))
    )));
  }

  /**
   * Scan external resources for inline links, and add the resource url if set.
   *
   * @return Url[]
   */
  public function scanExternalResource(ExternalResource $externalResource): array
  {
    $urls = $this->scanText($externalResource->getDescription(), new UrlContext(ExternalResource::class, $externalResource->getId(), 'description'));

    if ($externalResource->getUrl()) {
      $urls[] = $this->createUrl($externalResource->getUrl(), new UrlContext(ExternalResource::class, $externalResource->getId(), 'url'));
    }

    return $urls;
  }

  /**
   * Scan contributors for inline links, and add the contributor url if set.
   *
   * @return Url[]
   */
  public function scanContributors(Contributor $contributor): array
  {
    $urls = $this->scanText($contributor->getDescription(), new UrlContext(Contributor::class, $contributor->getId(), 'description'));

    if ($contributor->getUrl()) {
      $urls[] = $this->createUrl($contributor->getUrl(), new UrlContext(Contributor::class, $contributor->getId(), 'url'));
    }

    return $urls;
  }

  /**
   * Scan learning outcomes for inline links.
   *
   * @return Url[]
   */
  public function scanLearningOutcome(LearningOutcome $learningOutcome): array
  {
    return $this->scanText($learningOutcome->getText(), new UrlContext(LearningOutcome::class, $learningOutcome->getId(), 'text'));
  }

  /**
   * Scan learning path for inline links.
   *
   * @return Url[]
   */
  public function scanLearningPath(LearningPath $learningPath): array
  {
    return $this->scanText($learningPath->getIntroduction(), new UrlContext(LearningPath::class, $learningPath->getId(), 'introduction'));
  }

  /**
   * Scan a text for inline links.
   *
   * @return Url[]
   */
  public function scanText(?string $text, ?UrlContext $context = null): array
  {
    if ($text === null) {
      return [];
    }

    return array_values(array_unique($this->_scanText($text, $context)));
  }

  private function _scanText(string $text, ?UrlContext $context = null): array
  {
    $matches = [];
    $result  = [];
    if (false === preg_match_all($this->urlPattern, $text, $matches)) {
      // Regex search failed, ignore
      return $result;
    }
    if (!isset($matches[0]) || !isset($matches[1])) {
      // No results
      return $result;
    }

    // Prepare context
    $context ??= new UrlContext(self::class);
    $inlineContext = $context->asInline();

    // Convert matches
    foreach ($matches[0] as $key => $match) {
      // If it starts with src or href, it is linked
      $inline = true;
      if (0 === mb_stripos((string)$match, 'src') ||
          0 === mb_stripos((string)$match, 'href')) {
        $inline = false;
      }

      $result[] = $this->createUrl($matches[1][$key], $inline ? $inlineContext : $context);
    }

    // Find linked internals
    if (false === preg_match_all($this->internalLinkPattern, $text, $matches)) {
      // Regex failed, ignore
      return $result;
    }
    if (!isset($matches[0]) || !isset($matches[1])) {
      // No results
      return $result;
    }

    // Convert matches
    foreach ($matches[1] as $match) {
      $result[] = $this->createUrl($match, $context);
    }

    return $result;
  }

  /** Create an URL class from the given url string. */
  private function createUrl(string $url, UrlContext $context): Url
  {
    $url = trim($url);

    // Url is internal if it matches the routing context host
    return new Url($url, 1 === preg_match($this->internalPattern, $url), $context);
  }
}
