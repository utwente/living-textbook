<?php

namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WikiController
 *
 * @author BobV
 *
 * @Route("/wiki")
 */
class WikiController
{

  /**
   * @Route("/show")
   *
   * @Template("single_column.html.twig")
   */
  public function show(){
    return [];
  }
}
