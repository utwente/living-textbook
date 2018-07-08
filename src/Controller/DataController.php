<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\LearningOutcome;
use App\Entity\RelationType;
use App\Entity\StudyArea;
use App\Form\Data\DownloadType;
use App\Form\Data\DuplicateType;
use App\Form\Data\JsonUploadType;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\LearningOutcomeRepository;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DataController
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/data", requirements={"_studyArea"="\d+"})
 */
class DataController extends Controller
{

  /**
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
    // Retrieve the relation types as cache
    $relationTypes = $relationTypeRepo->findBy(['studyArea' => $requestStudyArea->getStudyArea()]);

    // Retrieve the concepts
    $concepts = $conceptRepo->findByStudyAreaOrderedByName($requestStudyArea->getStudyArea());

    // Return as JSON
    $groups = ["Default"];
    if ($export) $groups[] = "relations";
    $json = $serializer->serialize($concepts, 'json', SerializationContext::create()->setGroups($groups));

    return new JsonResponse($json, Response::HTTP_OK, [], true);
  }

  /**
   * @Route("/upload")
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param SerializerInterface    $serializer
   * @param TranslatorInterface    $translator
   * @param EntityManagerInterface $em
   * @param RelationTypeRepository $relationTypeRepo
   *
   * @return array
   */
  public function upload(Request $request, RequestStudyArea $requestStudyArea, SerializerInterface $serializer, TranslatorInterface $translator,
                         EntityManagerInterface $em, RelationTypeRepository $relationTypeRepo)
  {
    $form = $this->createForm(JsonUploadType::class, ['studyArea' => $requestStudyArea->getStudyArea()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Handle new upload
      $data = $form->getData();

      // Check file format, then load json data
      if ($data['json'] instanceof UploadedFile) {
        try {
          try {
            $jsonData = $serializer->deserialize(file_get_contents($data['json']->getPathname()), 'array', 'json');
          } catch (\Exception $e) {
            throw new \InvalidArgumentException("", 0, $e);
          }

          // Check fields
          if (!array_key_exists('nodes', $jsonData) ||
              !array_key_exists('links', $jsonData)
          ) {
            throw new \InvalidArgumentException();
          }

          // Resolve the link types
          $linkTypes = array();
          foreach ($jsonData['links'] as $jsonLink) {

            if (!array_key_exists('relationName', $jsonLink)) {
              throw new \InvalidArgumentException();
            }

            // Check whether already cached
            $linkName = $jsonLink['relationName'];
            if (!array_key_exists($linkName, $linkTypes)) {

              // Retrieve from database
              $linkType = $relationTypeRepo->findOneBy(['name' => $linkName, 'studyArea' => $data['studyArea']]);
              if ($linkType) {
                $linkTypes[$linkName] = $linkType;
              } else {
                // Create new link type
                $linkTypes[$linkName] = (new RelationType())->setStudyArea($data['studyArea'])->setName($linkName);
                $em->persist($linkTypes[$linkName]);
              }
            }
          }
          $em->flush();

          // Create a new concept for every entry
          /** @var Concept[] $concepts */
          $concepts = array();
          foreach ($jsonData['nodes'] as $key => $jsonNode) {
            if (!array_key_exists('label', $jsonNode)) {
              throw new \InvalidArgumentException();
            }

            $concepts[$key] = (new Concept())->setName($jsonNode['label']);
            $concepts[$key]->setStudyArea($data['studyArea']);
            $em->persist($concepts[$key]);
          }

          // Create the links
          foreach ($jsonData['links'] as $jsonLink) {
            if (!array_key_exists('target', $jsonLink) ||
                !array_key_exists('relationName', $jsonLink) ||
                !array_key_exists('source', $jsonLink)) {
              throw new \InvalidArgumentException();
            }

            $relation = new ConceptRelation();
            $relation->setTarget($concepts[$jsonLink['target']]);
            $relation->setRelationType($linkTypes[$jsonLink['relationName']]);
            $concepts[$jsonLink['source']]->addOutgoingRelation($relation);
          }

          // Save the data
          $em->flush();
          $this->addFlash('success', $translator->trans('data.json-uploaded'));
          $this->redirectToRoute('app_data_upload');

        } catch (\InvalidArgumentException $e) {
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
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param Request                   $request
   * @param RequestStudyArea          $requestStudyArea
   * @param SerializerInterface       $serializer
   * @param EntityManagerInterface    $em
   * @param ConceptRepository         $conceptRepo
   * @param ConceptRelationRepository $conceptRelationRepo
   * @param RelationTypeRepository    $relationTypeRepo
   *
   * @return array|Response
   */
  public function download(Request $request, RequestStudyArea $requestStudyArea, SerializerInterface $serializer, EntityManagerInterface $em,
                           ConceptRepository $conceptRepo, ConceptRelationRepository $conceptRelationRepo, RelationTypeRepository $relationTypeRepo)
  {
    // Use a form due to the fact that in the future, there will probably be a type selection here (json/owl)
    $form = $this->createForm(DownloadType::class);
    $form->handleRequest($request);

    $studyArea = $requestStudyArea->getStudyArea();
    if ($form->isSubmitted()) {
      // Retrieve the relation types as cache
      $relationTypes = $relationTypeRepo->findBy(['studyArea' => $studyArea]);

      // Retrieve the concepts
      $concepts = $conceptRepo->findByStudyAreaOrderedByName($studyArea);
      $links    = $conceptRelationRepo->findByConcepts($concepts);

      // Detach the data from the ORM
      $idMap = [];
      foreach ($concepts as $key => $concept) {
        assert($concept instanceof Concept);
        $idMap[$concept->getId()] = $key;
      }
      $mappedLinks = [];
      foreach ($links as &$link) {
        assert($link instanceof ConceptRelation);
        $mappedLinks[] = [
            'target'       => $idMap[$link->getTargetId()],
            'source'       => $idMap[$link->getSourceId()],
            'relationName' => $link->getRelationName(),
        ];
      }

      // Create JSON data
      {
        // Return as JSON
        $json = $serializer->serialize([
            'nodes' => $concepts,
            'links' => $mappedLinks,
        ], 'json', SerializationContext::create()->setGroups(['download_json']));

        $response = new JsonResponse($json, Response::HTTP_OK, [], true);
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s_export.json', mb_strtolower(preg_replace('/[^\p{L}\p{N}]/u', '_', $studyArea->getName())))
        ));

        return $response;
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
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param Request                   $request
   * @param RequestStudyArea          $requestStudyArea
   * @param ConceptRelationRepository $conceptRelationRepo
   * @param LearningOutcomeRepository $learningOutcomeRepo
   * @param EntityManagerInterface    $em
   * @param TranslatorInterface       $trans
   *
   * @return array|Response
   */
  public function duplicate(Request $request, RequestStudyArea $requestStudyArea,
                            ConceptRelationRepository $conceptRelationRepo, LearningOutcomeRepository $learningOutcomeRepo,
                            EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Create form to select the concepts for this study area
    $newStudyArea = (new StudyArea())->setOwner($this->getUser())->setAccessType(StudyArea::ACCESS_PRIVATE);
    $form         = $this->createForm(DuplicateType::class, [
        'studyArea' => $newStudyArea,
    ], [
        'current_study_area' => $requestStudyArea->getStudyArea(),
        'new_study_area'     => $newStudyArea,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $data      = $form->getData();
      $selectAll = $data['select_all'];
      if ($selectAll) {
        $concepts = $requestStudyArea->getStudyArea()->getConcepts();
      } else {
        $concepts = $data['concepts'];
      }

      // Persist the new study area
      $em->persist($newStudyArea);

      // Duplicate the study area learning outcomes
      $learningOutcomes    = $learningOutcomeRepo->findForConcepts($concepts->toArray());
      $newLearningOutcomes = [];
      foreach ($learningOutcomes as $learningOutcome) {
        $newLearningOutcome = (new LearningOutcome())
            ->setStudyArea($newStudyArea)
            ->setNumber($learningOutcome->getNumber())
            ->setName($learningOutcome->getName())
            ->setText($learningOutcome->getText());

        $em->persist($newLearningOutcome);
        $newLearningOutcomes[$learningOutcome->getId()] = $newLearningOutcome;
      }

      // TODO with #84
      // Add external resources

      // Duplicate the concepts
      /** @var Concept[] $newConcepts */
      $newConcepts     = [];
      $priorKnowledges = [];
      foreach ($concepts as $concept) {
        $newConcept = new Concept();
        $newConcept
            ->setName($concept->getName())
            ->setIntroduction($newConcept->getIntroduction()->setText($concept->getIntroduction()->getText()))
            ->setTheoryExplanation($newConcept->getTheoryExplanation()->setText($concept->getTheoryExplanation()->getText()))
            ->setHowTo($newConcept->getHowTo()->setText($concept->getHowTo()->getText()))
            ->setExamples($newConcept->getExamples()->setText($concept->getExamples()->getText()))
            ->setSelfAssessment($newConcept->getSelfAssessment()->setText($concept->getSelfAssessment()->getText()))
            ->setStudyArea($newStudyArea);

        // Set learning outcomes
        foreach ($concept->getLearningOutcomes() as $learningOutcome) {
          $newConcept->addLearningOutcome($newLearningOutcomes[$learningOutcome->getId()]);
        }

        // Save current prior knowledge to update them later when the concept map is complete
        $priorKnowledges[$concept->getId()] = $concept->getPriorKnowledge();

        $newConcepts[$concept->getId()] = $newConcept;
        $em->persist($newConcept);
      }

      // Loop the concepts again to add the prior knowledge
      foreach ($newConcepts as $oldId => &$newConcept) {
        foreach ($priorKnowledges[$oldId] as $priorKnowledge) {
          /** @var Concept $priorKnowledge */
          if (array_key_exists($priorKnowledge->getId(), $newConcepts)) {
            $newConcept->addPriorKnowledge($newConcepts[$priorKnowledge->getId()]);
          }
        }
      }

      // Duplicate the relations and relation types for the study area
      $conceptRelations    = $conceptRelationRepo->findByConcepts($concepts->toArray());
      $newRelationTypes    = [];
      $newConceptRelations = [];
      foreach ($conceptRelations as $conceptRelation) {
        // Skip relation for concepts that aren't duplicated
        if (!array_key_exists($conceptRelation->getSource()->getId(), $newConcepts)
            || !array_key_exists($conceptRelation->getTarget()->getId(), $newConcepts)) {
          continue;
        }

        $relationType = $conceptRelation->getRelationType();

        // Duplicate relation type, if not done yet
        if (!array_key_exists($relationType->getId(), $newRelationTypes)) {
          $newRelationType = (new RelationType())
              ->setStudyArea($newStudyArea)
              ->setName($relationType->getName());

          $newRelationTypes[$relationType->getId()] = $newRelationType;
          $em->persist($newRelationType);
        }

        // Duplicate relation
        $newConceptRelation = (new ConceptRelation())
            ->setSource($newConcepts[$conceptRelation->getSource()->getId()])
            ->setTarget($newConcepts[$conceptRelation->getTarget()->getId()])
            ->setRelationType($newRelationTypes[$relationType->getId()])
            ->setIncomingPosition($conceptRelation->getIncomingPosition())
            ->setOutgoingPosition($conceptRelation->getOutgoingPosition());

        $newConceptRelations[] = $conceptRelation;
        $em->persist($newConceptRelation);
      }

      // Save the data
      $em->flush();

      $this->addFlash('success', $trans->trans('data.concepts-duplicated'));

      return $this->redirectToRoute('app_default_dashboard');
    }

    return [
        'form'      => $form->createView(),
        'studyArea' => $requestStudyArea->getStudyArea(),
    ];
  }

}
