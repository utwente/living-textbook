<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Form\Concept\EditConceptType;
use App\Repository\ConceptRepository;
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
    $form = $this->createForm(EditConceptType::class, $concept);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
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
   * @Route("/{concept}", requirements={"concept"="\d+"})
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
