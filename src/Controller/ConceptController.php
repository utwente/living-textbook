<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\ConceptRelation;
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

    // Map original relations
    $originalRelations = new ArrayCollection();
    foreach ($concept->getRelations() as $relation) {
      $originalRelations->add($relation);
    }
    $originalIndirectRelations = new ArrayCollection();
    foreach ($concept->getIndirectRelations() as $indirectRelation) {
      $originalIndirectRelations->add($indirectRelation);
    }

    $form = $this->createForm(EditConceptType::class, $concept, [
        'concept' => $concept,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Check resources
      foreach ($concept->getExternalResources()->getResources() as $resource) {
        if ($resource->getResourceCollection() === NULL) {
          $resource->setResourceCollection($concept->getExternalResources());
        }
      };

      // Check relations
      foreach ($concept->getRelations() as $relation) {
        if ($relation->getSource() === NULL) {
          $relation->setSource($concept);
        }
      }
      foreach ($concept->getIndirectRelations() as $indirectRelation) {
        if ($indirectRelation->getTarget() === NULL) {
          $indirectRelation->setTarget($concept);
        }
      }

      // Remove outdated resources
      foreach ($originalResources as $originalResource) {
        if (false === $concept->getExternalResources()->getResources()->contains($originalResource)) {
          $em->remove($originalResource);
        }
      }

      // Remove outdated relations
      foreach ($originalRelations as $originalRelation) {
        if (false === $concept->getRelations()->contains($originalRelation)) {
          $em->remove($originalRelation);
        }
      }
      foreach ($originalIndirectRelations as $originalIndirectRelation) {
        if (false === $concept->getIndirectRelations()->contains($originalIndirectRelation)) {
          $em->remove($originalIndirectRelation);
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
