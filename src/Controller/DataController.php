<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\DuplicationUtils\StudyAreaDuplicator;
use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\ExternalResource;
use App\Entity\LearningOutcome;
use App\Entity\RelationType;
use App\Entity\StudyArea;
use App\Excel\StudyAreaStatusBuilder;
use App\Export\ExportService;
use App\Form\Data\DownloadType;
use App\Form\Data\DuplicateType;
use App\Form\Data\JsonUploadType;
use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use App\Repository\LearningPathRepository;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\UrlUtils\UrlScanner;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use InvalidArgumentException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DataController
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/data", requirements={"_studyArea"="\d+"})
 */
class DataController extends AbstractController
{

  /**
   * Export for concept browser and search. Search part currently isn't used, but it kept for now.
   *
   * @Route("/export", name="app_data_export", options={"expose"=true}, defaults={"export"=true})
   * @Route("/search", name="app_data_search", options={"expose"=true}, defaults={"export"=false})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param bool                   $export
   * @param RelationTypeRepository $relationTypeRepo
   * @param ConceptRepository      $conceptRepo
   * @param SerializerInterface    $serializer
   * @param RequestStudyArea       $requestStudyArea
   *
   * @return JsonResponse
   */
  public function export(bool $export, RelationTypeRepository $relationTypeRepo, ConceptRepository $conceptRepo,
                         SerializerInterface $serializer, RequestStudyArea $requestStudyArea)
  {
    /** @noinspection PhpUnusedLocalVariableInspection Retrieve the relation types as cache */
    $relationTypes = $relationTypeRepo->findBy(['studyArea' => $requestStudyArea->getStudyArea()]);

    // Retrieve the concepts
    $concepts = $conceptRepo->findForStudyAreaOrderedByName($requestStudyArea->getStudyArea());

    // Return as JSON
    $groups = ["Default"];
    if ($export) $groups[] = "relations";
    $json = $serializer->serialize($concepts, 'json', SerializationContext::create()->setGroups($groups));

    return new JsonResponse($json, Response::HTTP_OK, [], true);
  }

  /**
   * @Route("/excel")
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @param RequestStudyArea       $requestStudyArea
   * @param StudyAreaStatusBuilder $builder
   *
   * @return Response
   *
   * @throws Exception
   */
  public function excelStatus(RequestStudyArea $requestStudyArea, StudyAreaStatusBuilder $builder)
  {
    return $builder->build($requestStudyArea->getStudyArea());
  }

  /**
   * @Route("/upload")
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_default_dashboard", subject="requestStudyArea")
   *
   * @param Request                   $request
   * @param RequestStudyArea          $requestStudyArea
   * @param SerializerInterface       $serializer
   * @param TranslatorInterface       $translator
   * @param EntityManagerInterface    $em
   * @param RelationTypeRepository    $relationTypeRepo
   * @param ValidatorInterface        $validator
   * @param LearningOutcomeRepository $learningOutcomeRepository
   *
   * @return array
   * @throws NonUniqueResultException
   */
  public function upload(
      Request $request, RequestStudyArea $requestStudyArea, SerializerInterface $serializer, TranslatorInterface $translator,
      EntityManagerInterface $em, RelationTypeRepository $relationTypeRepo, ValidatorInterface $validator,
      LearningOutcomeRepository $learningOutcomeRepository)
  {
    $form = $this->createForm(JsonUploadType::class, ['studyArea' => $requestStudyArea->getStudyArea()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Handle new upload
      $data = $form->getData();

      // Check file format, then load json data
      if ($data['json'] instanceof UploadedFile) {
        try {

          // Expand default time limit for large imports
          set_time_limit(600); // 10 minutes

          try {
            $contents = mb_convert_encoding(file_get_contents($data['json']->getPathname()), 'UTF-8', 'UTF-8');
            $jsonData = $serializer->deserialize($contents, 'array', 'json');
          } catch (\Exception $e) {
            throw new InvalidArgumentException("", 0, $e);
          }

          // Check fields
          if (!array_key_exists('nodes', $jsonData) || !is_array($jsonData['nodes']) ||
              !array_key_exists('links', $jsonData) || !is_array($jsonData['links'])
          ) {
            throw new InvalidArgumentException();
          }

          // Resolve the link types
          $linkTypes = array();
          $studyArea = $data['studyArea'];
          foreach ($jsonData['links'] as $jsonLink) {

            if (!array_key_exists('relationName', $jsonLink)) {
              throw new InvalidArgumentException();
            }

            // Check whether already cached
            $linkName = $jsonLink['relationName'];
            if (!array_key_exists($linkName, $linkTypes)) {

              // Retrieve from database
              $linkType = $relationTypeRepo->findOneBy(['name' => $linkName, 'studyArea' => $studyArea]);
              if ($linkType) {
                $linkTypes[$linkName] = $linkType;
              } else {
                // Create new link type
                $linkTypes[$linkName] = (new RelationType())->setStudyArea($studyArea)->setName($linkName);
                if (count($validator->validate($linkTypes[$linkName])) > 0) {
                  throw new InvalidArgumentException();
                };
                $em->persist($linkTypes[$linkName]);
              }
            }
          }
          $em->flush();

          // Create a new concept for every entry
          /** @var Concept[] $concepts */
          $concepts = array();
          foreach ($jsonData['nodes'] as $key => $jsonNode) {
            if (!array_key_exists('label', $jsonNode) || $jsonNode['label'] === NULL) {
              throw new InvalidArgumentException();
            }

            $concepts[$key] = (new Concept())->setName($jsonNode['label']);
            if (array_key_exists('definition', $jsonNode) && $jsonNode['definition'] !== NULL) {
              $concepts[$key]->setDefinition($jsonNode['definition']);
            }
            $concepts[$key]->setStudyArea($studyArea);
            if (count($validator->validate($concepts[$key])) > 0) {
              throw new InvalidArgumentException();
            };
            $em->persist($concepts[$key]);
          }

          // Create the links
          foreach ($jsonData['links'] as $jsonLink) {
            if (!array_key_exists('target', $jsonLink) ||
                !array_key_exists('relationName', $jsonLink) ||
                !array_key_exists('source', $jsonLink)) {
              throw new InvalidArgumentException();
            }

            $relation = new ConceptRelation();
            $relation->setTarget($concepts[$jsonLink['target']]);
            $relation->setRelationType($linkTypes[$jsonLink['relationName']]);
            $concepts[$jsonLink['source']]->addOutgoingRelation($relation);
            if (count($validator->validate($relation)) > 0) {
              throw new InvalidArgumentException();
            };
          }

          if (array_key_exists('learning_outcomes', $jsonData)) {
            if (!is_array($jsonData['learning_outcomes'])) {
              throw new InvalidArgumentException();
            }

            $learningOutcomeNumber = $learningOutcomeRepository->findUnusedNumberInStudyArea($studyArea);
            foreach ($jsonData['learning_outcomes'] as $jsonLearningOutcome) {
              if (!array_key_exists('label', $jsonLearningOutcome) ||
                  !array_key_exists('definition', $jsonLearningOutcome) ||
                  !array_key_exists('isLearningOutcomeOf', $jsonLearningOutcome) ||
                  !is_array($jsonLearningOutcome['isLearningOutcomeOf'])) {
                throw new InvalidArgumentException();
              }

              $learningOutcome = new LearningOutcome();
              $learningOutcome->setName($jsonLearningOutcome['label']);
              $learningOutcome->setText($jsonLearningOutcome['definition']);
              $learningOutcome->setNumber($learningOutcomeNumber);
              $learningOutcome->setStudyArea($studyArea);
              foreach ($jsonLearningOutcome['isLearningOutcomeOf'] as $linkedConceptKey) {
                $concepts[$linkedConceptKey]->addLearningOutcome($learningOutcome);
              }
              if (count($validator->validate($learningOutcome)) > 0) {
                throw new InvalidArgumentException();
              };
              $em->persist($learningOutcome);
              $learningOutcomeNumber++;
            }
          }

          if (array_key_exists('external_resources', $jsonData)) {
            if (!is_array($jsonData['external_resources'])) {
              throw new InvalidArgumentException();
            }

            foreach ($jsonData['external_resources'] as $jsonExternalResource) {
              if (!array_key_exists('name', $jsonExternalResource) ||
                  !array_key_exists('isExternalResourceOf', $jsonExternalResource) ||
                  !is_array($jsonExternalResource['isExternalResourceOf'])) {
                throw new InvalidArgumentException();
              }

              // Create the external resource
              $externalResource = (new ExternalResource())
                  ->setTitle($jsonExternalResource['name'])
                  ->setStudyArea($studyArea);
              if (array_key_exists('description', $jsonExternalResource)) {
                $externalResource->setDescription($jsonExternalResource['description']);
              }
              if (array_key_exists('url', $jsonExternalResource)) {
                $externalResource->setUrl($jsonExternalResource['url']);
              }

              // Map to related concepts
              foreach ($jsonExternalResource['isExternalResourceOf'] as $linkedConceptKey) {
                $concepts[$linkedConceptKey]->addExternalResource($externalResource);
              }

              // Validate & persist
              if (count($validator->validate($externalResource)) > 0) {
                throw new InvalidArgumentException();
              };
              $em->persist($externalResource);
            }
          }

          // Save the data
          $em->flush();
          $this->addFlash('success', $translator->trans('data.json-uploaded'));
          $this->redirectToRoute('app_data_upload');

        } catch (InvalidArgumentException $e) {
          $this->addFlash('error', $translator->trans('data.json-incorrect'));
        }
      }
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/download")
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @param Request          $request
   * @param RequestStudyArea $requestStudyArea
   * @param ExportService    $exportService
   *
   * @return array|Response
   */
  public function download(Request $request, RequestStudyArea $requestStudyArea, ExportService $exportService)
  {
    $form = $this->createForm(DownloadType::class);
    $form->handleRequest($request);

    $studyArea = $requestStudyArea->getStudyArea();
    if ($form->isSubmitted()) {
      return $exportService->export($studyArea, $form->getData()['type']);
    }

    return [
        'studyArea' => $studyArea,
        'form'      => $form->createView(),
    ];
  }

  /**
   * @Route("/duplicate")
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param Request                    $request
   * @param RequestStudyArea           $requestStudyArea
   * @param TranslatorInterface        $trans
   * @param EntityManagerInterface     $em
   * @param UrlScanner                 $urlScanner
   * @param RouterInterface            $router
   * @param AbbreviationRepository     $abbreviationRepo
   * @param ConceptRelationRepository  $conceptRelationRepo
   * @param ExternalResourceRepository $externalResourceRepo
   * @param LearningOutcomeRepository  $learningOutcomeRepo
   * @param LearningPathRepository     $learningPathRepo
   *
   * @return array|Response
   * @throws \Exception
   */
  public function duplicate(Request $request, RequestStudyArea $requestStudyArea, TranslatorInterface $trans,
                            EntityManagerInterface $em, UrlScanner $urlScanner, RouterInterface $router,
                            AbbreviationRepository $abbreviationRepo, ConceptRelationRepository $conceptRelationRepo,
                            ExternalResourceRepository $externalResourceRepo, LearningOutcomeRepository $learningOutcomeRepo,
                            LearningPathRepository $learningPathRepo)
  {
    // Create form to select the concepts for this study area
    $studyAreaToDuplicate = $requestStudyArea->getStudyArea();
    $newStudyArea         = (new StudyArea())
        ->setOwner($this->getUser())
        ->setAccessType(StudyArea::ACCESS_PRIVATE)
        ->setDescription($studyAreaToDuplicate->getDescription())
        ->setPrintHeader($studyAreaToDuplicate->getPrintHeader())
        ->setPrintIntroduction($studyAreaToDuplicate->getPrintIntroduction());

    $form = $this->createForm(DuplicateType::class, [
        'studyArea' => $newStudyArea,
    ], [
        'current_study_area' => $studyAreaToDuplicate,
        'new_study_area'     => $newStudyArea,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $data      = $form->getData();
      $selectAll = $data['select_all'];
      if ($selectAll) {
        $concepts = $studyAreaToDuplicate->getConcepts();
      } else {
        $concepts = $data['concepts'];
      }

      // Duplicate the data
      $duplicator = new StudyAreaDuplicator(
          $this->getParameter('kernel.project_dir'), $em, $urlScanner, $router,
          $abbreviationRepo, $conceptRelationRepo, $externalResourceRepo, $learningOutcomeRepo,
          $learningPathRepo, $studyAreaToDuplicate, $newStudyArea, $concepts->toArray());
      $duplicator->duplicate();

      $this->addFlash('success', $trans->trans('data.concepts-duplicated'));

      // Load reloading page in order to switch to the duplicated study area
      return $this->render('reloading_fullscreen.html.twig', [
          'reloadUrl' => $this->generateUrl('_home', ['_studyArea' => $newStudyArea->getId()]),
      ]);
    }

    return [
        'form'      => $form->createView(),
        'studyArea' => $studyAreaToDuplicate,
    ];
  }

}
