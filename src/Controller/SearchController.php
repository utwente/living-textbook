<?php

namespace App\Controller;

use App\Entity\Annotation;
use App\Entity\Contracts\SearchableInterface;
use App\Entity\User;
use App\Repository\AbbreviationRepository;
use App\Repository\AnnotationRepository;
use App\Repository\ConceptRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use App\Request\Wrapper\RequestStudyArea;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SearchController
 *
 * @Route("/{_studyArea}/search", requirements={"_studyArea"="\d+"})
 */
class SearchController extends AbstractController
{

  /**
   * @Route("/{s}", name="search", requirements={"s"=".*"}, defaults={"s"=null})
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param string|null                $s
   * @param RequestStudyArea           $requestStudyArea
   * @param TranslatorInterface        $translator
   * @param AbbreviationRepository     $abbreviationRepository
   * @param ConceptRepository          $conceptRepository
   * @param ExternalResourceRepository $externalResourceRepository
   * @param LearningOutcomeRepository  $learningOutcomeRepository
   * @param AnnotationRepository       $annotationRepository
   *
   * @return array
   */
  public function search(
      ?string $s, RequestStudyArea $requestStudyArea, TranslatorInterface $translator,
      AbbreviationRepository $abbreviationRepository, ConceptRepository $conceptRepository,
      ExternalResourceRepository $externalResourceRepository, LearningOutcomeRepository $learningOutcomeRepository,
      AnnotationRepository $annotationRepository)
  {
    $result = [];

    if (NULL === $s) {
      return $result;
    }

    // Verify data
    if (strlen($s) < 3) {
      $result['error'] = $translator->trans('search.invalid-short');
    } elseif (strlen($s) > 100) {
      $result['error'] = $translator->trans('search.invalid-long');
    }

    if (array_key_exists('error', $result)) {
      return $result;
    }

    $studyArea        = $requestStudyArea->getStudyArea();
    $search           = mb_strtolower($s);
    $result['search'] = $search;

    // We just retrieve all data, to filter them locally on the content.
    $result['conceptData']          = $this->searchData($conceptRepository->findForStudyAreaOrderedByName($studyArea, true), $search);
    $result['abbreviationData']     = $this->searchData($abbreviationRepository->findForStudyArea($studyArea), $search);
    $result['externalResourceData'] = $this->searchData($externalResourceRepository->findForStudyArea($studyArea), $search);
    $result['learningOutcomeData']  = $this->searchData($learningOutcomeRepository->findForStudyArea($studyArea), $search);

    // Retrieve annotation data, which is easier to do here
    $user = $this->getUser();
    assert($user instanceof User);
    if ($user) {
      $userId         = $user->getId();
      $allAnnotations = $annotationRepository->getForUserAndStudyArea($user, $studyArea);
      $ownAnnotations = array_filter($allAnnotations, function (Annotation $annotation) use ($userId) {
        return $annotation->getUserId() == $userId;
      });

      $result['ownAnnotationsData'] = $this->groupAnnotationsByConcept($this->searchData($ownAnnotations, $search));
      $result['allAnnotationsData'] = $this->groupAnnotationsByConcept($this->searchData($allAnnotations, $search));
    }

    return $result;
  }

  /**
   * @param SearchableInterface[] $data
   * @param string                $search
   *
   * @return array
   */
  private function searchData(array $data, string $search): array
  {
    $data = array_map(function (SearchableInterface $element) use ($search) {
      return $element->searchIn($search);
    }, $data);

    $data = array_filter($data, function ($element) {
      return $this->filterSortData($element);
    });

    usort($data, array(SearchController::class, "sortSearchData"));

    return $data;
  }

  /**
   * Group the result by the concept in the object
   *
   * @param array $data
   *
   * @return array
   */
  private function groupAnnotationsByConcept(array $data): array
  {
    $result = [];

    array_map(function ($item) use (&$result) {
      $annotation = $item['_data'];
      assert($annotation instanceof Annotation);
      $concept = $annotation->getConcept();

      // Set concept key for sorting purposes
      $conceptKey = $concept->getName() . $concept->getId();
      if (!array_key_exists($conceptKey, $result)) {
        $result[$conceptKey] = ['count' => 0, 'items' => []];
      }
      $result[$conceptKey]['count']   += count($item['results']);
      $result[$conceptKey]['items'][] = $item;
    }, $data);

    ksort($result);

    return $result;
  }

  private function filterSortData($element): bool
  {
    return array_key_exists('results', $element) && count($element['results']) > 0;
  }

  public static function sortSearchData($a, $b)
  {
    $reduceFunction = function ($carry, $item) {
      return $item['prio'] > $carry ? $item['prio'] : $carry;
    };

    $ap = array_reduce($a['results'], $reduceFunction, 0);
    $bp = array_reduce($b['results'], $reduceFunction, 0);

    if ($ap == $bp) {
      return strcmp($a['_title'], $b['_title']);
    }

    return $bp - $ap;
  }

  /**
   * Create a search result
   *
   * @param int          $prio      Result priority
   * @param string       $property  The result property
   * @param string|array $data      Result data. Should be a string in the common cases, but can be a data array when
   *                                more data is required
   *
   * @return array
   */
  public static function createResult(int $prio, string $property, $data): array
  {
    return [
        'prio'     => $prio,
        'property' => $property,
        'data'     => $data,
    ];
  }

}
