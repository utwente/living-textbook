<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\RelationType;
use App\Form\Data\JsonUploadType;
use App\Repository\ConceptRepository;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
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
   * @Route("/export/{_studyArea}", name="app_data_export", options={"expose"=true}, defaults={"export"=true, "_studyArea"=null}, requirements={"_studyArea": "\d+"})
   * @Route("/search/{_studyArea}", name="app_data_search", options={"expose"=true}, defaults={"_studyArea"=null}, requirements={"_studyArea": "\d+"})
   *
   * @param bool                   $export
   * @param RelationTypeRepository $relationTypeRepo
   * @param ConceptRepository      $conceptRepo
   * @param SerializerInterface    $serializer
   * @param RequestStudyArea       $studyArea
   *
   * @return JsonResponse
   */
  public function export(bool $export = false, RelationTypeRepository $relationTypeRepo, ConceptRepository $conceptRepo,
                         SerializerInterface $serializer, RequestStudyArea $studyArea)
  {
    // Retrieve the relation types as cache
    $relationTypes = $relationTypeRepo->findAll();

    // Retrieve the concepts
    $concepts = $conceptRepo->findByStudyAreaOrderedByName($studyArea->getStudyArea());

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
   * @param TranslatorInterface    $translator
   * @param EntityManagerInterface $em
   * @param RelationTypeRepository $relationTypeRepo
   *
   * @return array
   */
  public function upload(Request $request, SerializerInterface $serializer, TranslatorInterface $translator,
                         EntityManagerInterface $em, RelationTypeRepository $relationTypeRepo)
  {
    $form = $this->createForm(JsonUploadType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Handle new upload
      $data = $form->getData();

      // Check file format, then load json data
      if ($data['json'] instanceof UploadedFile) {
        $jsonData = $serializer->deserialize(file_get_contents($data['json']->getPathname()), 'array', 'json');

        try {
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
              $linkType = $relationTypeRepo->findOneBy(['name' => $linkName]);
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

}
