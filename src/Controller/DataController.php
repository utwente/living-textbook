<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\ConceptStudyArea;
use App\Entity\RelationType;
use App\Entity\StudyArea;
use App\Form\Data\JsonUploadType;
use App\Repository\ConceptRepository;
use App\Repository\RelationTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DataController
 *
 * @author BobV
 *
 * @Route("/data")
 */
class DataController extends Controller
{

  /**
   * @Route("/export/{studyArea}", name="app_data_export", options={"expose"=true}, defaults={"export"=true, "studyArea"=null}, requirements={"studyArea": "\d+"})
   * @Route("/search/{studyArea}", name="app_data_search", options={"expose"=true}, defaults={"studyArea"=null}, requirements={"studyArea": "\d+"})
   *
   * @param bool                   $export
   * @param EntityManagerInterface $em
   * @param SerializerInterface    $serializer
   * @param StudyArea|null         $studyArea
   *
   * @return JsonResponse
   */
  public function export(bool $export = false, EntityManagerInterface $em, SerializerInterface $serializer, ?StudyArea $studyArea)
  {
    // Retrieve the relations type as cache
    $relationTypeRepo = $em->getRepository('App:RelationType');
    assert($relationTypeRepo instanceof RelationTypeRepository);
    $relationTypes = $relationTypeRepo->findAll();

    // Retrieve the concepts
    $conceptRepo = $em->getRepository('App:Concept');
    assert($conceptRepo instanceof ConceptRepository);
    if($studyArea !== null) {
      $concepts = $conceptRepo->findByStudyAreaOrderedByName($studyArea);
    } else {
      $concepts = $conceptRepo->findAllOrderedByName();
    }

    // Return as JSON
    $groups = ["Default"];
    if ($export) $groups[] = "relations";
    $json = $serializer->serialize($concepts, 'json', SerializationContext::create()->setGroups($groups));

    return new JsonResponse($json, Response::HTTP_OK, [], true);
  }

  /**
   * @Route("/upload")
   * @Template()
   *
   * @param Request                $request
   * @param SerializerInterface    $serializer
   *
   * @param TranslatorInterface    $translator
   *
   * @param EntityManagerInterface $em
   *
   * @return array
   */
  public function upload(Request $request, SerializerInterface $serializer, TranslatorInterface $translator, EntityManagerInterface $em)
  {
    $form = $this->createForm(JsonUploadType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Handle new upload
      $data = $form->getData();

      // Check file format, then load json data
      if ($data['json'] instanceof UploadedFile) {
        $jsonData = $serializer->deserialize(file_get_contents($data['json']->getPathname()), 'array', 'json');

        // Check fields
        if (array_key_exists('nodes', $jsonData) &&
            array_key_exists('label', $jsonData['nodes']) &&
            array_key_exists('links', $jsonData) &&
            array_key_exists('source', $jsonData['links']) &&
            array_key_exists('target', $jsonData['links']) &&
            array_key_exists('relationName', $jsonData['links'])
        ) {

          // Resolve the link types
          $linkTypes = array();
          foreach ($jsonData['links'] as $jsonLink) {

            // Check whether already cached
            $linkName = $jsonLink['relationName'];
            if (!array_key_exists($linkName, $linkTypes)) {

              // Retrieve from database
              $linkType = $em->getRepository('App:RelationType')->findOneBy(['name' => $linkName]);
              if ($linkType) {
                $linkTypes[$linkName] = $linkType;
              } else {
                // Create new link type
                $linkTypes[$linkName] = (new RelationType())->setName($linkName);
                $em->persist($linkTypes[$linkName]);
              }
            }
          }
          $em->flush();

          // Create a new concept for every entry
          /** @var Concept[] $concepts */
          $concepts = array();
          foreach ($jsonData['nodes'] as $key => $jsonNode) {
            $concepts[$key] = (new Concept())->setName($jsonNode['label']);
            foreach($data['studyArea'] as $studyArea)
            {
              $conceptStudyArea = new ConceptStudyArea();
              $conceptStudyArea->setStudyArea($studyArea);
              $concepts[$key]->addStudyArea($conceptStudyArea);
            }
            $em->persist($concepts[$key]);
          }

          // Create the links
          foreach ($jsonData['links'] as $jsonLink) {
            $relation = new ConceptRelation();
            $relation->setTarget($concepts[$jsonLink['target']]);
            $relation->setRelationType($linkTypes[$jsonLink['relationName']]);
            $concepts[$jsonLink['source']]->addRelation($relation);
          }

          // Save the data
          $em->flush();
          $this->addFlash('success', $translator->trans('data.json-uploaded'));
          $this->redirectToRoute('app_data_upload');
        } else {
          $this->addFlash('error', $translator->trans('data.json-incorrect'));
        }
      }
    }

    return [
        'form' => $form->createView(),
    ];
  }

}
