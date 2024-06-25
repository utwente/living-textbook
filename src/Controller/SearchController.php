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
use App\Security\Voters\StudyAreaVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_studyArea<\d+>}/search')]
class SearchController extends AbstractController
{
  #[Route('/{s<.*>?}', name: 'search')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function search(
    ?string $s, RequestStudyArea $requestStudyArea, TranslatorInterface $translator,
    AbbreviationRepository $abbreviationRepository, ConceptRepository $conceptRepository,
    ExternalResourceRepository $externalResourceRepository, LearningOutcomeRepository $learningOutcomeRepository,
    AnnotationRepository $annotationRepository): Response
  {
    $result = [];

    if (null === $s) {
      return $this->render('search/search.html.twig', $result);
    }

    // Verify data
    if (strlen($s) < 3) {
      $result['error'] = $translator->trans('search.invalid-short');
    } elseif (strlen($s) > 100) {
      $result['error'] = $translator->trans('search.invalid-long');
    }

    if (array_key_exists('error', $result)) {
      return $this->render('search/search.html.twig', $result);
    }

    $studyArea        = $requestStudyArea->getStudyArea();
    $search           = mb_strtolower($s);
    $result['search'] = $search;

    // We just retrieve all data, to filter them locally on the content.
    $result['conceptData']          = $this->searchData($conceptRepository->findForStudyAreaOrderedByName($studyArea, true, true), $search);
    $result['instanceData']         = $this->searchData($conceptRepository->findForStudyAreaOrderedByName($studyArea, true, false, true), $search);
    $result['abbreviationData']     = $this->searchData($abbreviationRepository->findForStudyArea($studyArea), $search);
    $result['externalResourceData'] = $this->searchData($externalResourceRepository->findForStudyArea($studyArea), $search);
    $result['learningOutcomeData']  = $this->searchData($learningOutcomeRepository->findForStudyArea($studyArea), $search);

    // Retrieve annotation data, which is easier to do here
    $user = $this->getUser();
    if ($user) {
      assert($user instanceof User);
      $userId         = $user->getId();
      $allAnnotations = $annotationRepository->getForUserAndStudyArea($user, $studyArea);
      $ownAnnotations = array_filter($allAnnotations, fn (Annotation $annotation) => $annotation->getUserId() == $userId);

      $result['ownAnnotationsData'] = $this->groupAnnotationsByConcept($this->searchData($ownAnnotations, $search));
      $result['allAnnotationsData'] = $this->groupAnnotationsByConcept($this->searchData($allAnnotations, $search));
    }

    return $this->render('search/search.html.twig', $result);
  }

  /** @param SearchableInterface[] $data */
  private function searchData(array $data, string $search): array
  {
    $data = array_map(fn (SearchableInterface $element) => $element->searchIn($search), $data);

    $data = array_filter($data, fn ($element) => $this->filterSortData($element));

    usort($data, SearchController::sortSearchData(...));

    return $data;
  }

  /** Group the result by the concept in the object. */
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
      $result[$conceptKey]['count']   += is_countable($item['results']) ? count($item['results']) : 0;
      $result[$conceptKey]['items'][] = $item;
    }, $data);

    ksort($result);

    return $result;
  }

  private function filterSortData($element): bool
  {
    return array_key_exists('results', $element) && (is_countable($element['results']) ? count($element['results']) : 0) > 0;
  }

  public static function sortSearchData($a, $b): int
  {
    $reduceFunction = fn ($carry, $item) => max($item['prio'], $carry);

    $ap = array_reduce($a['results'], $reduceFunction, 0);
    $bp = array_reduce($b['results'], $reduceFunction, 0);

    if ($ap == $bp) {
      return strcmp((string)$a['_title'], (string)$b['_title']);
    }

    return $bp - $ap;
  }

  /**
   * Create a search result.
   *
   * @param int          $prio     Result priority
   * @param string       $property The result property
   * @param string|array $data     Result data. Should be a string in the common cases, but can be a data array when
   *                               more data is required
   */
  public static function createResult(int $prio, string $property, string|array $data): array
  {
    return [
      'prio'     => $prio,
      'property' => $property,
      'data'     => $data,
    ];
  }
}
