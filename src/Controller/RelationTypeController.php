<?php

namespace App\Controller;

use App\Entity\RelationType;
use App\Form\RelationType\EditRelationTypeType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Repository\RelationTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class RelationTypeController
 *
 * @Route("/relationtype")
 */
class RelationTypeController extends Controller
{
  /**
   * @Route("/add")
   * @Template()
   *
   * @param Request                $request
   * @param EntityManagerInterface $em
   *
   * @return array|Response
   */
  public function add(Request $request, EntityManagerInterface $em)
  {
    // Create new
    $relationType = new RelationType();

    $form = $this->createForm(EditRelationTypeType::class, $relationType);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      // Save the data
      $em->persist($relationType);
      $em->flush();

      // Check for forward to list
      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_relationtype_list');
      }

      // Forward to show page
      return $this->redirectToRoute('app_relationtype_edit', ['relationType' => $relationType->getId()]);
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{relationType}", requirements={"relationType"="\d+"})
   * @Template()
   *
   * @param Request                $request
   * @param RelationType           $relationType
   * @param EntityManagerInterface $em
   *
   * @return Response|array
   */
  public function edit(Request $request, RelationType $relationType, EntityManagerInterface $em)
  {
    // Create form and handle request
    $form = $this->createForm(EditRelationTypeType::class, $relationType);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      // Save the data
      $em->flush();

      // Check for forward to list
      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_relationtype_list');
      }

      // Forward to show
      return $this->redirectToRoute('app_relationtype_edit', ['relationType' => $relationType->getId()]);
    }

    return [
        'relationType' => $relationType,
        'form'         => $form->createView(),
    ];
  }

  /**
   * @Route("/list")
   * @Template()
   *
   * @param RelationTypeRepository $repo
   *
   * @return array
   */
  public function list(RelationTypeRepository $repo)
  {
    $relationTypes = $repo->findBy(['deletedAt' => NULL]);

    return [
        'relationTypes' => $relationTypes,
    ];
  }

  /**
   * @Route("/remove/{relationType}", requirements={"relationType"="\d+"})
   * @Template()
   *
   * @param Request                $request
   * @param RelationType           $relationType
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|RedirectResponse
   */
  public function remove(Request $request, RelationType $relationType, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_relationtype_list',
    ]);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $em->remove($relationType);
      $em->flush();

      $this->addFlash('success', $trans->trans('relation-type.removed', ['%item%' => $relationType->getName()]));

      return $this->redirectToRoute('app_relationtype_list');
    }

    return [
        'relationType' => $relationType,
        'form'         => $form->createView(),
    ];
  }
}
