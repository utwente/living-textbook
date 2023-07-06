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
use App\Entity\Tag;
use App\Entity\User;
use App\Excel\StudyAreaStatusBuilder;
use App\Exception\DataImportException;
use App\Export\ExportService;
use App\Form\Data\DownloadType;
use App\Form\Data\DuplicateType;
use App\Form\Data\JsonUploadType;
use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\ContributorRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use App\Repository\LearningPathRepository;
use App\Repository\RelationTypeRepository;
use App\Repository\TagRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Router\LtbRouter;
use App\UrlUtils\UrlScanner;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DataController.
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
    $groups = ['Default'];
    if ($export) {
      $groups[] = 'relations';
    }
    /** @phan-suppress-next-line PhanTypeMismatchArgument */
    $json = $serializer->serialize($concepts, 'json', SerializationContext::create()->setGroups($groups));

    return new JsonResponse($json, Response::HTTP_OK, [], true);
  }

  /**
   * @Route("/excel")
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @throws Exception
   *
   * @return Response
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
   * @throws NonUniqueResultException
   */
  public function upload(
      Request $request, RequestStudyArea $requestStudyArea, SerializerInterface $serializer, TranslatorInterface $translator,
      EntityManagerInterface $em, RelationTypeRepository $relationTypeRepo, ValidatorInterface $validator,
      LearningOutcomeRepository $learningOutcomeRepository): array|Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    if ($studyArea->isReviewModeEnabled()) {
      // We do not support upload in areas where the review mode is enabled
      $this->addFlash('warning', $translator->trans('data.upload-json-disabled-review-mode'));

      return $this->redirectToRoute('app_default_dashboard');
    }

    $form = $this->createForm(JsonUploadType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      try {
        // Handle new upload
        $json = $form->getData()['json'];

        // Check file format
        if (!($json instanceof UploadedFile)) {
          throw new DataImportException('File upload failed');
        }

        // Expand default time limit for large imports
        set_time_limit(600); // 10 minutes

        try {
          $contents = mb_convert_encoding(file_get_contents($json->getPathname()), 'UTF-8', 'UTF-8');

          // Extra json check as we now allow text/html uploads
          if (!$this->couldBeJson($contents)) {
            throw new DataImportException('Invalid JSON contents detected');
          }

          $jsonData = $serializer->deserialize($contents, 'array', 'json');
        } catch (\Exception $e) {
          throw new DataImportException('Deserialization error, did you upload a valid JSON file?', 0, $e);
        }

        // Check fields
        if (!array_key_exists('nodes', $jsonData) || !is_array($jsonData['nodes']) ||
            !array_key_exists('links', $jsonData) || !is_array($jsonData['links'])
        ) {
          throw new DataImportException('Expected "nodes" and "links" properties to be an array!');
        }

        // Resolve the link types
        $linkTypes = [];
        foreach ($jsonData['links'] as $jsonLink) {
          if (!array_key_exists('relationName', $jsonLink)) {
            throw new DataImportException(
                sprintf('Missing required "relationName" property on link: %s', json_encode($jsonLink)));
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
              if ($validator->validate($linkTypes[$linkName])->count() > 0) {
                throw new DataImportException(
                    sprintf('Could not create the relation type: %s', json_encode($jsonLink)));
              }
              $em->persist($linkTypes[$linkName]);
            }
          }
        }
        $em->flush();

        // Create a new concept for every entry
        /** @var Concept[] $concepts */
        $concepts = [];
        foreach ($jsonData['nodes'] as $key => $jsonNode) {
          if (!array_key_exists('label', $jsonNode) || $jsonNode['label'] === null) {
            throw new DataImportException(
                sprintf('Missing required "label" property on node: %s', json_encode($jsonNode)));
          }

          $concepts[$key] = (new Concept())->setName($jsonNode['label']);
          if (array_key_exists('definition', $jsonNode) && $jsonNode['definition'] !== null) {
            $concepts[$key]->setDefinition($jsonNode['definition']);
          }

          if (array_key_exists('explanation', $jsonNode) && $jsonNode['explanation'] !== null) {
            $theoryExplanation = $concepts[$key]->getTheoryExplanation();
            $theoryExplanation->setText($jsonNode['explanation']);

            if ($validator->validate($theoryExplanation)->count() > 0) {
              throw new DataImportException(
                  sprintf('Could not create the concept explanation: %s', json_encode($theoryExplanation)));
            }
          }

          if (array_key_exists('introduction', $jsonNode) && $jsonNode['introduction'] !== null) {
            $introduction = $concepts[$key]->getIntroduction();
            $introduction->setText($jsonNode['introduction']);

            if ($validator->validate($introduction)->count() > 0) {
              throw new DataImportException(
                  sprintf('Could not create the concept introduction: %s', json_encode($introduction)));
            }
          }

          if (array_key_exists('examples', $jsonNode) && $jsonNode['examples'] !== null) {
            $examples = $concepts[$key]->getExamples();
            $examples->setText($jsonNode['examples']);

            if ($validator->validate($examples)->count() > 0) {
              throw new DataImportException(
                  sprintf('Could not create the concept examples: %s', json_encode($examples)));
            }
          }

          if (array_key_exists('additionalResources', $jsonNode) && $jsonNode['additionalResources'] !== null) {
            $additionalResources = $concepts[$key]->getAdditionalResources();
            $additionalResources->setText($jsonNode['additionalResources']);

            if ($validator->validate($additionalResources)->count() > 0) {
              throw new DataImportException(
                  sprintf('Could not create the concept additional resources: %s', json_encode($additionalResources)));
            }
          }

          if (array_key_exists('howTo', $jsonNode) && $jsonNode['howTo'] !== null) {
            $howTo = $concepts[$key]->getHowTo();
            $howTo->setText($jsonNode['howTo']);

            if ($validator->validate($howTo)->count() > 0) {
              throw new DataImportException(
                  sprintf('Could not create the concept how to: %s', json_encode($howTo)));
            }
          }

          if (array_key_exists('selfAssessment', $jsonNode) && $jsonNode['selfAssessment'] !== null) {
            $selfAssessment = $concepts[$key]->getSelfAssessment();
            $selfAssessment->setText($jsonNode['selfAssessment']);

            if ($validator->validate($selfAssessment)->count() > 0) {
              throw new DataImportException(
                  sprintf('Could not create the concept self assessment: %s', json_encode($selfAssessment)));
            }
          }
            
          if (array_key_exists('imagePath', $jsonNode) && $jsonNode['imagePath'] !== null && $jsonNode['imagePath'] !== '') {
            $concepts[$key]->setImagePath($jsonNode['imagePath']);
          }

          $concepts[$key]->setStudyArea($studyArea);
          if ($validator->validate($concepts[$key])->count() > 0) {
            throw new DataImportException(
                sprintf('Could not create the concept: %s', json_encode($jsonNode)));
          }
          $em->persist($concepts[$key]);
        }

        // Create the links
        foreach ($jsonData['links'] as $jsonLink) {
          if (!array_key_exists('target', $jsonLink) ||
              !array_key_exists('relationName', $jsonLink) ||
              !array_key_exists('source', $jsonLink)) {
            throw new DataImportException(
                sprintf('Missing one ore more required properties "target", "relationName" or "source" from link: %s', json_encode($jsonLink)));
          }

          if (!array_key_exists($jsonLink['source'], $concepts)) {
            throw new DataImportException(
                sprintf('Link references non-existing source node: %s', json_encode($jsonLink)));
          }
          if (!array_key_exists($jsonLink['target'], $concepts)) {
            throw new DataImportException(
                sprintf('Link references non-existing target node: %s', json_encode($jsonLink)));
          }

          $relation = new ConceptRelation();
          $relation->setTarget($concepts[$jsonLink['target']]);
          $relation->setRelationType($linkTypes[$jsonLink['relationName']]);
          $concepts[$jsonLink['source']]->addOutgoingRelation($relation);
          if ($validator->validate($relation)->count() > 0) {
            throw new DataImportException(
                sprintf('Could not create the concept relation: %s', json_encode($jsonLink)));
          }
        }

        if (array_key_exists('learning_outcomes', $jsonData)) {
          if (!is_array($jsonData['learning_outcomes'])) {
            throw new DataImportException(sprintf('When set, the "learning_outcomes" property must be an array!'));
          }

          $learningOutcomeNumber = $learningOutcomeRepository->findUnusedNumberInStudyArea($studyArea);
          foreach ($jsonData['learning_outcomes'] as $jsonLearningOutcome) {
            if (!array_key_exists('label', $jsonLearningOutcome) ||
                !array_key_exists('definition', $jsonLearningOutcome) ||
                !array_key_exists('isLearningOutcomeOf', $jsonLearningOutcome)) {
              throw new DataImportException(
                  sprintf('Missing one ore more required properties "label", "definition" or "isLearningOutcomeOf" from learning outcome: %s', json_encode($jsonLearningOutcome)));
            }

            if (!is_array($jsonLearningOutcome['isLearningOutcomeOf'])) {
              throw new DataImportException(
                  sprintf('The "isLearningOutcomeOf" property must be an array in learning outcome: %s', json_encode($jsonLearningOutcome)));
            }

            $learningOutcome = new LearningOutcome();
            /* @phan-suppress-next-line PhanTypeMismatchArgument */
            $learningOutcome->setName($jsonLearningOutcome['label']);
            /* @phan-suppress-next-line PhanTypeMismatchArgument */
            $learningOutcome->setText($jsonLearningOutcome['definition']);
            $learningOutcome->setNumber($learningOutcomeNumber);
            $learningOutcome->setStudyArea($studyArea);
            foreach ($jsonLearningOutcome['isLearningOutcomeOf'] as $linkedConceptKey) {
              if (!array_key_exists($linkedConceptKey, $concepts)) {
                throw new DataImportException(
                    sprintf('The referenced node %d does not exist in learning outcome: %s', $linkedConceptKey, json_encode($jsonLearningOutcome)));
              }

              $concepts[$linkedConceptKey]->addLearningOutcome($learningOutcome);
            }
            if ($validator->validate($learningOutcome)->count() > 0) {
              throw new DataImportException(
                  sprintf('Could not create the concept learning outcome: %s', json_encode($jsonLearningOutcome)));
            }
            $em->persist($learningOutcome);
            $learningOutcomeNumber++;
          }
        }

        if (array_key_exists('external_resources', $jsonData)) {
          if (!is_array($jsonData['external_resources'])) {
            throw new DataImportException(sprintf('When set, the "external_resources" property must be an array!'));
          }

          foreach ($jsonData['external_resources'] as $jsonExternalResource) {
            if (!array_key_exists('name', $jsonExternalResource) ||
                !array_key_exists('isExternalResourceOf', $jsonExternalResource)) {
              throw new DataImportException(
                  sprintf('Missing one ore more required properties "name" or "isExternalResourceOf" from external resource: %s', json_encode($jsonExternalResource)));
            }

            if (!is_array($jsonExternalResource['isExternalResourceOf'])) {
              throw new DataImportException(
                  sprintf('The "isExternalResourceOf" property must be an array in external resource: %s', json_encode($jsonExternalResource)));
            }

            // Create the external resource
            $externalResource = (new ExternalResource())
                /* @phan-suppress-next-line PhanTypeMismatchArgument */
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
              if (!array_key_exists($linkedConceptKey, $concepts)) {
                throw new DataImportException(
                    sprintf('The referenced node %d does not exist in external resource: %s', $linkedConceptKey, json_encode($jsonExternalResource)));
              }

              $concepts[$linkedConceptKey]->addExternalResource($externalResource);
            }

            // Validate & persist
            if ($validator->validate($externalResource)->count() > 0) {
              throw new DataImportException(
                  sprintf('Could not create the external resource: %s', json_encode($jsonExternalResource)));
            }
            $em->persist($externalResource);
          }
        }

        // Tags
        if (array_key_exists('tags', $jsonData)) {
          if (!is_array($jsonData['tags'])) {
            throw new DataImportException(sprintf('When set, the "tags" property must be an array!'));
          }

          foreach ($jsonData['tags'] as $jsonTag) {
            if (!array_key_exists('name', $jsonTag) ||
                !array_key_exists('isTagOf', $jsonTag)) {
              throw new DataImportException(
                  sprintf('Missing one ore more required properties "name" or "isTagOf" from tag: %s', json_encode($jsonTag)));
            }
            if (!is_array($jsonTag['isTagOf'])) {
              throw new DataImportException(
                  sprintf('The "isTagOf" property must be an array in tag: %s', json_encode($jsonTag)));
            }

            // Create the tag
            $tag = (new Tag())
                /* @phan-suppress-next-line PhanTypeMismatchArgument */
                ->setName($jsonTag['name'])
                ->setStudyArea($studyArea);

            if (array_key_exists('description', $jsonTag)) {
              $tag->setDescription($jsonTag['description']);
            }

            // Map to related concepts
            foreach ($jsonTag['isTagOf'] as $taggedConceptKey) {
              if (!array_key_exists($taggedConceptKey, $concepts)) {
                throw new DataImportException(
                    sprintf('The referenced node %d does not exist in tag: %s', $taggedConceptKey, json_encode($jsonTag)));
              }

              $concepts[$taggedConceptKey]->addTag($tag);
            }

            // Validate & persist
            if ($validator->validate($tag)->count() > 0) {
              throw new DataImportException(
                  sprintf('Could not create the tag: %s', json_encode($jsonTag)));
            }
            $em->persist($tag);
          }
        }

        // Prior knowledge
        if (array_key_exists('priorKnowledge', $jsonData)) {
          foreach ($jsonData['priorKnowledge'] as $jsonPriorKnowledge) {
            if (!array_key_exists('node', $jsonPriorKnowledge) || !array_key_exists('isPriorKnowledgeOf', $jsonPriorKnowledge)) {
              throw new DataImportException(
                sprintf('Missing one ore more required properties "node" or "isPriorKnowledgeOf" from prior knowledge: %s', json_encode($jsonPriorKnowledge)));
            }

            if (!array_key_exists($jsonPriorKnowledge['node'], $concepts)) {
              throw new DataImportException(
                  sprintf('Prior knowledge references non-existing "node": %s', json_encode($jsonPriorKnowledge)));
            }
            if (!array_key_exists($jsonPriorKnowledge['isPriorKnowledgeOf'], $concepts)) {
              throw new DataImportException(
                  sprintf('Prior knowledge references non-existing "isPriorKnowledgeOf": %s', json_encode($jsonPriorKnowledge)));
            }
            $concepts[$jsonPriorKnowledge['node']]->addPriorKnowledge($concepts[$jsonPriorKnowledge['isPriorKnowledgeOf']]);
          }
        }

        // Save the data
        $em->flush();
        $this->addFlash('success', $translator->trans('data.json-uploaded'));
        $this->redirectToRoute('app_data_upload');
      } catch (DataImportException $e) {
        $this->addFlash('error', $translator->trans('data.json-incorrect', ['%message%' => $e->getMessage()]));
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
   */
  public function download(Request $request, RequestStudyArea $requestStudyArea, ExportService $exportService, TranslatorInterface $translator): array|Response
  {
    $studyArea = $requestStudyArea->getStudyArea();
    $form = $this->createForm(DownloadType::class, null, [
      'current_study_area' => $studyArea,
    ]);
    $form->handleRequest($request);   
    
    if ($form->isSubmitted()) {     
      $exportResponse =$exportService->export($studyArea, $form->getData()['type']);

      if ($studyArea->isUrlExportEnabled() && $studyArea->getExportUrl() !== null 
        && $exportResponse->getStatusCode() == Response::HTTP_OK) {
        $client = HttpClient::create();

        $response = $client->request('PUT', 
          sprintf('%s/%s.json', $studyArea->getExportUrl(), $studyArea->getName(), $studyArea->getId()), 
          [
            'headers' => [
                'Content-Type' => 'application/json',
          ],
          'body' => $exportResponse->getContent() ? $exportResponse->getContent() : ''
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == Response::HTTP_OK) {
          $this->addFlash(
            'success', sprintf('%s: "%s"', 
            $translator->trans('data.export-to-url-success'), 
            $studyArea->getExportUrl())
          );
          $this->redirectToRoute('app_data_download');
        } else {
          $this->addFlash('error', sprintf('%s: "%s".', 
            $translator->trans('data.export-to-url-error'), 
            $studyArea->getExportUrl())
          );          
        }
      } else {
        return $exportResponse;
      }      
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
   * @throws \Exception
   */
  public function duplicate(
      Request $request, RequestStudyArea $requestStudyArea, TranslatorInterface $trans,
      EntityManagerInterface $em, UrlScanner $urlScanner, LtbRouter $router,
      AbbreviationRepository $abbreviationRepo, ConceptRelationRepository $conceptRelationRepo,
      ContributorRepository $contributorRepository, ExternalResourceRepository $externalResourceRepo,
      LearningOutcomeRepository $learningOutcomeRepo, LearningPathRepository $learningPathRepo,
      TagRepository $tagRepository): array|Response
  {
    $user = $this->getUser();
    assert($user instanceof User);

    // Create form to select the concepts for this study area
    $studyAreaToDuplicate = $requestStudyArea->getStudyArea();
    $newStudyArea         = (new StudyArea())
        ->setOwner($user)
        ->setAccessType(StudyArea::ACCESS_PRIVATE)
        ->setDescription($studyAreaToDuplicate->getDescription())
        ->setPrintHeader($studyAreaToDuplicate->getPrintHeader())
        ->setPrintIntroduction($studyAreaToDuplicate->getPrintIntroduction());

    $form = $this->createForm(DuplicateType::class, [
        DuplicateType::NEW_STUDY_AREA => $newStudyArea,
    ], [
        'current_study_area' => $studyAreaToDuplicate,
        'new_study_area'     => $newStudyArea,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $data      = $form->getData();
      $selectAll = $data[DuplicateType::SELECT_ALL];
      if ($selectAll) {
        $concepts = $studyAreaToDuplicate->getConcepts();
      } else {
        $concepts = $data[DuplicateType::CONCEPTS];
      }

      if ($data[DuplicateType::CHOICE] === DuplicateType::CHOICE_EXISTING) {
        $targetStudyArea = $data[DuplicateType::EXISTING_STUDY_AREA];
      } else {
        $targetStudyArea = $newStudyArea;
      }

      // Duplicate the data
      $duplicator = new StudyAreaDuplicator(
          $this->getParameter('kernel.project_dir'), $em, $urlScanner, $router,
          $abbreviationRepo, $conceptRelationRepo, $contributorRepository, $externalResourceRepo, $learningOutcomeRepo,
          $learningPathRepo, $tagRepository, $studyAreaToDuplicate, $targetStudyArea, $concepts->toArray());
      $duplicator->duplicate();

      $this->addFlash('success', $trans->trans('data.concepts-duplicated'));

      // Load reloading page in order to switch to the duplicated study area
      return $this->render('reloading_fullscreen.html.twig', [
          'reloadUrl' => $this->generateUrl('_home', ['_studyArea' => $targetStudyArea->getId()]),
      ]);
    }

    return [
        'form'      => $form->createView(),
        'studyArea' => $studyAreaToDuplicate,
    ];
  }

  /* Based on https://stackoverflow.com/a/45241792/1439286 */
  private function couldBeJson(mixed $value): bool
  {
    // Numeric strings are always valid JSON.
    if (is_numeric($value)) {
      return true;
    }

    // A non-string value can never be a JSON string.
    if (!is_string($value)) {
      return false;
    }

    // Any non-numeric JSON string must be longer than 2 characters.
    if (strlen($value) < 2) {
      return false;
    }

    // "null" is valid JSON string.
    if ('null' === $value) {
      return true;
    }

    // "true" and "false" are valid JSON strings.
    if ('true' === $value) {
      return true;
    }
    if ('false' === $value) {
      return false;
    }

    // Any other JSON string has to be wrapped in {}, [] or "".
    if ('{' != $value[0] && '[' != $value[0] && '"' != $value[0]) {
      return false;
    }

    // Verify that the trailing character matches the first character.
    $last_char = $value[strlen($value) - 1];
    if ('{' == $value[0] && '}' != $last_char) {
      return false;
    }
    if ('[' == $value[0] && ']' != $last_char) {
      return false;
    }
    if ('"' == $value[0] && '"' != $last_char) {
      return false;
    }

    return true;
  }
}
