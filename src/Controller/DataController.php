<?php

namespace App\Controller;

use App\Entity\Node;
use App\Entity\NodeRelation;
use App\Entity\RelationType;
use App\Form\Data\JsonUploadType;
use App\Repository\NodeRepository;
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
   * @Route("/export", name="app_data_export", options={"expose"=true}, defaults={"export"=true})
   * @Route("/search", name="app_data_search", options={"expose"=true})
   *
   * @param bool                   $export
   * @param EntityManagerInterface $em
   * @param SerializerInterface    $serializer
   *
   * @return JsonResponse
   */
  public function export(bool $export = false, EntityManagerInterface $em, SerializerInterface $serializer)
  {
    // Retrieve the relations type as cache
    $relationTypeRepo = $em->getRepository('App:RelationType');
    assert($relationTypeRepo instanceof RelationTypeRepository);
    $relationTypes = $relationTypeRepo->findAll();

    // Retrieve the nodes
    $nodeRepo = $em->getRepository('App:Node');
    assert($nodeRepo instanceof NodeRepository);
    $nodes = $nodeRepo->findAllOrderedByName();

    // Return as JSON
    $groups = ["Default"];
    if ($export) $groups[] = "relations";
    $json = $serializer->serialize($nodes, 'json', SerializationContext::create()->setGroups($groups));

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

          // Create a new node for every entry
          /** @var Node[] $nodes */
          $nodes = array();
          foreach ($jsonData['nodes'] as $key => $jsonNode) {
            $nodes[$key] = (new Node())->setName($jsonNode['label']);
            $em->persist($nodes[$key]);
          }

          // Create the links
          foreach ($jsonData['links'] as $jsonLink) {
            $relation = new NodeRelation();
            $relation->setTarget($nodes[$jsonLink['target']]);
            $relation->setRelationType($linkTypes[$jsonLink['relationName']]);
            $nodes[$jsonLink['source']]->addRelation($relation);
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
