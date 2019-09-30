<?php

namespace App\Controller;

use App\Repository\HelpRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class HelpController
 * Scoped per study area to prevent reload issues
 *
 * @Route("/{_studyArea}/help", requirements={"_studyArea"="\d+"})
 */
class HelpController extends AbstractController
{

  /**
   * Displays the available help documents
   *
   * @Route()
   * @Template()
   *
   * @param HelpRepository $helpRepository
   *
   * @return array
   *
   * @throws NoResultException
   * @throws NonUniqueResultException
   */
  public function index(HelpRepository $helpRepository)
  {
    return [
        'help' => $helpRepository->getCurrent(),
    ];
  }
}
