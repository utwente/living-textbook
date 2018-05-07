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
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class RelationTypeController
 *
 * @Route("/{_studyArea}/relationtype", requirements={"_studyArea"="\d+"})
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
    // Check if not removed
    if ($relationType->getDeletedAt() !== NULL){
      throw $this->createNotFoundException();
    }

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
    // Check if not already deleted
    if ($relationType->getDeletedAt() !== null){
      $this->addFlash('warning', $trans->trans('relation-type.removed-already', ['%item%' => $relationType->getName()]));

      return $this->redirectToRoute('app_relationtype_list');
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_relationtype_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      // Remove the relation type by setting the deletedAt/By manually
      $relationType->setDeletedAt(new \DateTime());
      $relationType->setDeletedBy($this->getUser() instanceof UserInterface ? $this->getUser()->getName() : 'anon.');
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
