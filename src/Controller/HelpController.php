<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class HelpController
 *
 * @Route("/help", requirements={"_studyArea"="\d+"})
 */
class HelpController extends AbstractController
{

  /**
   * Displays the available help documents
   *
   * @Route()
   * @Template()
   */
  public function index()
  {

  }
}
