<?php

namespace App\UrlScanner;

use App\Entity\Concept;
use App\Entity\ExternalResource;
use App\Entity\LearningOutcome;
use App\Entity\StudyArea;
use App\UrlScanner\Model\Url;
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
  private $urlPattern = '(?xi)\b((?:https?:(?:\/{1,3}|[a-z0-9%])|[a-z0-9.\-]+[.](?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\/)(?:[^\s()<>{}\[\]]+|\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\))+(?:\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’])|(?:(?<!@)[a-z0-9]+(?:[.\-][a-z0-9]+)*[.](?:com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|Ja|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\b\/?(?!@)))';

  /**
   * Pattern to determine whether or not the url is internal
   *
   * @var string
   */
  private $internalPattern;

  /**
   * Pattern to retrieve url from src and href attributes
   *
   * @var string
   */
  private $linkedPattern = '(?xi)[src|href]\s*=\s*["|\'](.+?)["|\']';

  /** @var RouterInterface */
  private $router;

  /**
   * UrlScanner constructor.
   *
   * @param RouterInterface $router
   */
  public function __construct(RouterInterface $router)
  {
    $this->router          = $router;
    $this->internalPattern = '(?xi)\b((?:https?:(?:\/{1,3}' . preg_quote($router->getContext()->getHost()) . ')))';
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return Url[]
   */
  public function scanStudyArea(StudyArea $studyArea): array
  {
    return $this->scanText($studyArea->getDescription());
  }

  /**
   * @param Concept $concept
   *
   * @return Url[]
   */
  public function scanConcept(Concept $concept): array
  {
    return array_unique(array_merge(
        $this->scanText($concept->getIntroduction()->getText()),
        $this->scanText($concept->getTheoryExplanation()->getText()),
        $this->scanText($concept->getHowTo()->getText()),
        $this->scanText($concept->getExamples()->getText()),
        $this->scanText($concept->getSelfAssessment()->getText())
    ), SORT_REGULAR);
  }

  /**
   * @param ExternalResource $externalResource
   *
   * @return Url[]
   */
  public function scanExternalResource(ExternalResource $externalResource): array
  {
    return $this->scanText($externalResource->getDescription());
  }

  /**
   * @param LearningOutcome $learningOutcome
   *
   * @return Url[]
   */
  public function scanLearningOutcome(LearningOutcome $learningOutcome): array
  {
    return $this->scanText($learningOutcome->getText());
  }

  /**
   * @param string $text
   *
   * @return Url[]
   */
  public function scanText(string $text): array
  {
    return array_unique(array_merge(
        $this->_scanForInline($text),
        $this->_scanForLinked($text)
    ), SORT_REGULAR);
  }

  /**
   * Scan the text for linked urls, in src/href items
   *
   * @param string $text
   *
   * @return array
   */
  private function _scanForLinked(string $text): array
  {
    return $this->_scan($text, $this->linkedPattern);
  }

  /**
   * Scan the text for inline url occurrences
   *
   * @param string $text
   *
   * @return Url[]
   */
  private function _scanForInline(string $text): array
  {
    return $this->_scan($text, $this->urlPattern);
  }

  /**
   * Scan the text
   *
   * @param string $text
   * @param string $pattern
   *
   * @return array
   */
  private function _scan(string $text, string $pattern): array
  {
    $matches = [];
    $result  = [];
    if (false === preg_match_all('#' . $pattern . '#', $text, $matches)) {
      // Regex search failed, ignore
      return $result;
    }
    if (!isset($matches[1])) {
      // No results
      return $result;
    }

    // Convert matches
    return array_map(function ($item) {
      return $this->createUrl($item);
    }, $matches[1]);
  }

  /**
   * Create an URL class from the given url string
   *
   * @param string $url
   *
   * @return Url
   */
  private function createUrl(string $url): Url
  {
    // Url is internal if it starts with a /, or matches the routing context host
    $internal =
        0 === mb_stripos($url, '/') ||
        1 === preg_match('#' . $this->internalPattern . '#', $url);

    return new Url($url, $internal);
  }
}
