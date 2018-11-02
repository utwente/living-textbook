<?php

namespace App\UrlUtils;

use App\Entity\Concept;
use App\Entity\ExternalResource;
use App\Entity\LearningOutcome;
use App\Entity\StudyArea;
use App\UrlUtils\Model\Url;
use App\UrlUtils\Model\UrlContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class UrlScanner
 *
 * The URL scanner class is responsible for scanning texts in order to isolate a list of URLS
 */
class UrlScanner
{

  /**
   * URL pattern
   * Stolen from https://gist.github.com/gruber/8891611
   *
   * @var string
   */
  private $urlPattern = '#(?xi)(?:(?:src|href)\s*=\s*["\']?\s*)?\b((?:https?:(?:\/{1,3}|[a-z0-9%])|[a-z0-9.\-]+[.](?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\/)(?:[^\s()<>{}\[\]]+|\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\))+(?:\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’])|(?:(?<!@)[a-z0-9]+(?:[.\-][a-z0-9]+)*[.](?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\b\/?(?!@)))#';

  /**
   * Pattern to determine whether or not the url is internal
   *
   * @var string
   */
  private $internalPattern;

  /**
   * URL pattern to find relative linked urls
   *
   * @var string
   */
  private $internalLinkPattern = '#(?xi)(?:src|href)\s*=\s*["\'](\/.+?)["\']#';

  /**
   * UrlScanner constructor.
   *
   * @param RouterInterface $router
   */
  public function __construct(RouterInterface $router)
  {
    $this->internalPattern = '#(?xi)\b((?:https?:(?:\/{1,3}' . preg_quote($router->getContext()->getHost()) . ')))#';
  }

  /**
   * Scan a study area for a inline links
   *
   * @param StudyArea $studyArea
   *
   * @return Url[]
   */
  public function scanStudyArea(StudyArea $studyArea): array
  {
    return $this->scanText($studyArea->getDescription(), new UrlContext(StudyArea::class, "description"));
  }

  /**
   * Scan a concept for inline links
   *
   * @param Concept $concept
   *
   * @return Url[]
   */
  public function scanConcept(Concept $concept): array
  {
    return array_values(array_unique(array_merge(
        $this->scanText($concept->getIntroduction()->getText(), new UrlContext(Concept::class, "introduction")),
        $this->scanText($concept->getTheoryExplanation()->getText(), new UrlContext(Concept::class, "theoryExplanation")),
        $this->scanText($concept->getHowTo()->getText(), new UrlContext(Concept::class, "howTo")),
        $this->scanText($concept->getExamples()->getText(), new UrlContext(Concept::class, "examples")),
        $this->scanText($concept->getSelfAssessment()->getText(), new UrlContext(Concept::class, "selfAssessment"))
    )));
  }

  /**
   * Scan external resources for inline links
   *
   * @param ExternalResource $externalResource
   *
   * @return Url[]
   */
  public function scanExternalResource(ExternalResource $externalResource): array
  {
    return $this->scanText($externalResource->getDescription(), new UrlContext(ExternalResource::class, "description"));
  }

  /**
   * Scan learning outcomes for inline links
   *
   * @param LearningOutcome $learningOutcome
   *
   * @return Url[]
   */
  public function scanLearningOutcome(LearningOutcome $learningOutcome): array
  {
    return $this->scanText($learningOutcome->getText(), new UrlContext(LearningOutcome::class, "text"));
  }

  /**
   * Scan a text for inline links
   *
   * @param string          $text
   * @param UrlContext|null $context
   *
   * @return Url[]
   */
  public function scanText(string $text, ?UrlContext $context = NULL): array
  {
    return array_values(array_unique($this->_scanText($text, $context)));
  }

  /**
   * @param string          $text
   * @param UrlContext|null $context
   *
   * @return array
   */
  private function _scanText(string $text, ?UrlContext $context = NULL): array
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
    $context       = $context ?? new UrlContext(self::class);
    $inlineContext = $context->asInline();

    // Convert matches
    foreach ($matches[0] as $key => $match) {
      // If it starts with src or href, it is linked
      $inline = true;
      if (0 === mb_stripos($match, 'src') ||
          0 === mb_stripos($match, 'href')) {
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

  /**
   * Create an URL class from the given url string
   *
   * @param string     $url
   * @param UrlContext $context
   *
   * @return Url
   */
  private function createUrl(string $url, UrlContext $context): Url
  {
    $url = trim($url);

    // Url is internal if it matches the routing context host
    return new Url($url, 1 === preg_match($this->internalPattern, $url), $context);
  }
}
