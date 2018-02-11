<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\ExternalResource;
use App\Form\Concept\EditConceptType;
use App\Repository\ConceptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConceptController
 *
 * @author BobV
 *
 * @Route("/concept")
 */
class ConceptController extends Controller
{

  /**
   * @Route("/edit/{concept}", requirements={"concept"="\d+"})
   * @Template()
   *
   * @param Request                $request
   * @param Concept                $concept
   * @param EntityManagerInterface $em
   *
   * @return Response|array
   */
  public function edit(Request $request, Concept $concept, EntityManagerInterface $em)
  {
    // Map original resources
    $originalResources = new ArrayCollection();
    foreach ($concept->getExternalResources()->getResources() as $resource) {
      $originalResources->add($resource);
    }

    $form = $this->createForm(EditConceptType::class, $concept);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Check resources
      $concept->getExternalResources()->getResources()->forAll(function ($key, $resource) use ($concept) {
        /** @var ExternalResource $resource */
        if ($resource->getResourceCollection() === NULL) {
          $resource->setResourceCollection($concept->getExternalResources());
        }
        return true;
      });

      // Remove outdated resources
      foreach ($originalResources as $originalResource) {
        if (false === $concept->getExternalResources()->getResources()->contains($originalResource)){
          $em->remove($originalResource);
        }
      }

      // Save the data
      $em->flush();

      return $this->redirectToRoute('app_concept_show', ['concept' => $concept->getId()]);
    }

    return [
        'concept' => $concept,
        'form'    => $form->createView(),
    ];
  }

  /**
   * @Route("/list")
   * @Template()
   *
   * @param EntityManagerInterface $em
   *
   * @return array
   */
  public function list(EntityManagerInterface $em)
  {
    $repo = $em->getRepository('App:Concept');
    assert($repo instanceof ConceptRepository);
    $concepts = $repo->findAllOrderedByName();

    return [
        'concepts' => $concepts,
    ];
  }

  /**
   * @Route("/{concept}", requirements={"concept"="\d+"}, options={"expose"=true})
   * @Template()
   *
   * @param Concept $concept
   *
   * @return array
   */
  public function show(Concept $concept)
  {
    return [
        'concept' => $concept,
    ];
  }

}
