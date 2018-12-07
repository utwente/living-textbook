<?php

namespace App\Controller;

use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use App\Request\Wrapper\RequestStudyArea;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SearchController
 *
 * @Route("/{_studyArea}/search", requirements={"_studyArea"="\d+"})
 */
class SearchController extends AbstractController
{

  /**
   * @Route("/", name="search")
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param Request                    $request
   * @param RequestStudyArea           $requestStudyArea
   * @param FormFactoryInterface       $formFactory
   * @param TranslatorInterface        $translator
   * @param AbbreviationRepository     $abbreviationRepository
   * @param ConceptRepository          $conceptRepository
   * @param ExternalResourceRepository $externalResourceRepository
   * @param LearningOutcomeRepository  $learningOutcomeRepository
   *
   * @return array
   */
  public function search(Request $request, RequestStudyArea $requestStudyArea, FormFactoryInterface $formFactory,
                         TranslatorInterface $translator, AbbreviationRepository $abbreviationRepository,
                         ConceptRepository $conceptRepository, ExternalResourceRepository $externalResourceRepository,
                         LearningOutcomeRepository $learningOutcomeRepository)
  {
    // Create the search form
    $form = $this->createSearchForm($formFactory, $translator);
    $form->handleRequest($request);

    $result = [
        'form' => $form->createView(),
    ];

    // Check for submission
    if (!$form->isSubmitted()) {
      return $result;
    }

    // Check for valid search term
    if (!$form->isValid()) {
      $this->addFlash('notice', $translator->trans('search.invalid-search'));

      return $result;
    }

    $studyArea        = $requestStudyArea->getStudyArea();
    $search           = mb_strtolower($form->getData()['s']);
    $result['search'] = $search;

    // We just retrieve all data, to filter them locally on the content. This is required to
    $result['conceptData']          = $this->searchData($conceptRepository->findForStudyAreaOrderedByName($studyArea, true), $search);
    $result['abbreviationData']     = $this->searchData($abbreviationRepository->findForStudyArea($studyArea), $search);
    $result['externalResourceData'] = $this->searchData($externalResourceRepository->findForStudyArea($studyArea), $search);
    $result['learningOutcomeData']  = $this->searchData($learningOutcomeRepository->findForStudyArea($studyArea), $search);

    return $result;
  }

  private function searchData(array $data, string $search): array
  {
    $data = array_map(function ($element) use ($search) {
      /** @noinspection PhpUndefinedMethodInspection */
      return $element->searchIn($search);
    }, $data);

    $data = array_filter($data, function ($element) {
      return $this->filterSortData($element);
    });

    usort($data, array(&$this, "sortSearchData"));

    return $data;
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

  public static function createResult(int $prio, string $property, string $data): array
  {
    return [
        'prio'     => $prio,
        'property' => $property,
        'data'     => $data,
    ];
  }

  /**
   * @Template()
   * @IsGranted("ROLE_USER")
   *
   * @param FormFactoryInterface $formFactory
   * @param TranslatorInterface  $translator
   *
   * @return array
   */
  public function searchForm(FormFactoryInterface $formFactory, TranslatorInterface $translator)
  {
    return [
        'form' => $this->createSearchForm($formFactory, $translator, false)->createView(),
    ];
  }

  /**
   * @param FormFactoryInterface $formFactory
   * @param TranslatorInterface  $translator
   * @param mixed                $label
   *
   * @return \Symfony\Component\Form\FormInterface
   */
  private function createSearchForm(FormFactoryInterface $formFactory, TranslatorInterface $translator, $label = 'search.search'): \Symfony\Component\Form\FormInterface
  {
    return $formFactory->createNamedBuilder('search_form')
        ->setAction($this->generateUrl('search'))
        ->add('s', TextType::class, [
            'label'       => $label,
            'hide_label'  => $label === false,
            'attr'        => [
                'placeholder' => $translator->trans('search.placeholder'),
            ],
            'constraints' => [
                new Length([
                    'min' => 3,
                    'max' => 100,
                ]),
            ],
        ])
        ->getForm();
  }

}
