<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Form\Concept\EditConceptType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Repository\ConceptRepository;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ConceptController
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/concept", requirements={"_studyArea"="\d+"})
 */
class ConceptController extends Controller
{
  /**
   * @Route("/add")
   * @Template()
   *
   * @param Request                $request
   * @param RequestStudyArea       $studyArea
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function add(Request $request, RequestStudyArea $studyArea, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Create new concept
    $concept = (new Concept())->setStudyArea($studyArea->getStudyArea());

    // Create form and handle request
    $form = $this->createForm(EditConceptType::class, $concept, [
        'concept' => $concept,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->persist($concept);
      $em->flush();

      $this->addFlash('success', $trans->trans('concept.saved', ['%item%' => $concept->getName()]));

      // Check for forward to list
      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_concept_list');
      }

      // Forward to show page
      return $this->redirectToRoute('app_concept_show', ['concept' => $concept->getId()]);
    }

    return [
        'concept' => $concept,
        'form'    => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{concept}", requirements={"concept"="\d+"})
   * @Template()
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Concept                $concept
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return Response|array
   */
  public function edit(Request $request, RequestStudyArea $requestStudyArea, Concept $concept, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check study area
    if ($concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    // Map original resources
    $originalResources = new ArrayCollection();
    foreach ($concept->getExternalResources()->getResources() as $resource) {
      $originalResources->add($resource);
    }

    // Map original relations
    $originalOutgoingRelations = new ArrayCollection();
    foreach ($concept->getOutgoingRelations() as $outgoingRelation) {
      $originalOutgoingRelations->add($outgoingRelation);
    }
    $originalIncomingRelations = new ArrayCollection();
    foreach ($concept->getIncomingRelations() as $incomingRelation) {
      $originalIncomingRelations->add($incomingRelation);
    }

    // Create form and handle request
    $form = $this->createForm(EditConceptType::class, $concept, [
        'concept' => $concept,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Remove outdated resources
      foreach ($originalResources as $originalResource) {
        if (false === $concept->getExternalResources()->getResources()->contains($originalResource)) {
          $em->remove($originalResource);
        }
      }

      // Remove outdated relations
      foreach ($originalOutgoingRelations as $originalOutgoingRelation) {
        if (false === $concept->getOutgoingRelations()->contains($originalOutgoingRelation)) {
          $em->remove($originalOutgoingRelation);
        }
      }
      foreach ($originalIncomingRelations as $originalIncomingRelation) {
        if (false === $concept->getIncomingRelations()->contains($originalIncomingRelation)) {
          $em->remove($originalIncomingRelation);
        }
      }

      // Save the data
      $em->flush();

      $this->addFlash('success', $trans->trans('concept.updated', ['%item%' => $concept->getName()]));

      // Check for forward to list
      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_concept_list');
      }

      // Forward to show
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
   * @param ConceptRepository $repo
   * @param RequestStudyArea  $studyArea
   *
   * @return array
   */
  public function list(ConceptRepository $repo, RequestStudyArea $studyArea)
  {
    $concepts = $repo->findByStudyAreaOrderedByName($studyArea->getStudyArea());

    return [
        'concepts' => $concepts,
    ];
  }

  /**
   * @Route("/remove/{concept}", requirements={"concept"="\d+"})
   * @Template()
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Concept                $concept
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function remove(Request $request, RequestStudyArea $requestStudyArea, Concept $concept, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check study area
    if ($concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route'        => 'app_concept_show',
        'cancel_route_params' => ['concept' => $concept->getId()],
    ]);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $em->remove($concept);
      $em->flush();

      $this->addFlash('success', $trans->trans('concept.removed', ['%item%' => $concept->getName()]));

      return $this->redirectToRoute('app_concept_list');
    }

    return [
        'concept' => $concept,
        'form'    => $form->createView(),
    ];
  }

  /**
   * @Route("/{concept}", requirements={"concept"="\d+"}, options={"expose"=true})
   * @Template()
   *
   * @param Concept          $concept
   * @param RequestStudyArea $requestStudyArea
   *
   * @return array
   */
  public function show(Concept $concept, RequestStudyArea $requestStudyArea)
  {
    // Check study area
    if ($concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    return [
        'concept' => $concept,
    ];
  }

}
