<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RemoveType;
use App\Form\User\AddFallbackUserType;
use App\Form\User\EditFallbackUserType;
use App\Form\User\UpdatePasswordType;
use App\Repository\StudyAreaRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UserController
 *
 * @Route("/{_studyArea}/users", requirements={"_studyArea"="\d+"})
 */
class UserController extends Controller
{

  /**
   * @Route("/fallback/add")
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @param Request                      $request
   * @param EntityManagerInterface       $em
   * @param UserPasswordEncoderInterface $encoder
   * @param TranslatorInterface          $trans
   *
   * @return array|Response
   */
  public function fallbackAdd(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, TranslatorInterface $trans)
  {

    $user = new User();
    $form = $this->createForm(AddFallbackUserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Encode the password
      $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

      // Save the new user
      $em->persist($user);
      $em->flush();

      $this->addFlash('success', $trans->trans('user.fallback.added', ['%user%' => $user->getDisplayName()]));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/fallback/edit/{user}", requirements={"user"="\d+"})
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @param Request                $request
   * @param User                   $user
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function fallbackEdit(Request $request, User $user, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check whether user is a fallback user
    if ($user->isOidc()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(EditFallbackUserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();

      $this->addFlash('success', $trans->trans('user.fallback.updated', ['%user%' => $user->getDisplayName()]));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    return [
        'user' => $user,
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/fallback/list")
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @param UserRepository $userRepository
   *
   * @return array
   */
  public function fallbackList(UserRepository $userRepository)
  {
    // Retrieve users
    return [
        'users' => $userRepository->getFallbackUsers(),
    ];
  }

  /**
   * @Route("/fallback/password/{user}", requirements={"user"="\d+"})
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @param Request                      $request
   * @param User                         $user
   * @param EntityManagerInterface       $em
   * @param UserPasswordEncoderInterface $encoder
   * @param TranslatorInterface          $trans
   *
   * @return array|Response
   */
  public function fallbackResetPassword(Request $request, User $user, EntityManagerInterface $em,
                                        UserPasswordEncoderInterface $encoder, TranslatorInterface $trans)
  {
    // Check whether user is a fallback user
    if ($user->isOidc()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(UpdatePasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $user->setPassword($encoder->encodePassword($user, $form->getData()['password']));
      $em->flush();

      $this->addFlash('success', $trans->trans('user.fallback.password-updated', [
          '%user%' => $user->getDisplayName(),
      ]));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    return [
        'user' => $user,
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/fallback/remove/{user}", requirements={"user"="\d+"})
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @param Request                $request
   * @param User                   $user
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   * @param StudyAreaRepository    $studyAreaRepository
   *
   * @return array|Response
   */
  public function fallbackRemove(Request $request, User $user, EntityManagerInterface $em, TranslatorInterface $trans, StudyAreaRepository $studyAreaRepository)
  {
    // Check whether user is a fallback user
    if ($user->isOidc()) {
      throw $this->createNotFoundException();
    }

    // Get owned study areas
    $studyAreas = $studyAreaRepository->findBy(['owner' => $user], ['name' => 'ASC']);

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_user_fallbacklist',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      foreach ($studyAreas as $studyArea) {
        $em->remove($studyArea);
      }
      $em->remove($user);
      $em->flush();

      $this->addFlash('success', $trans->trans('user.fallback.removed', [
          '%user%' => $user->getDisplayName(),
      ]));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    return [
        'user'       => $user,
        'studyAreas' => $studyAreas,
        'form'       => $form->createView(),
    ];
  }

}
