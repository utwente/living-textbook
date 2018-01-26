<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class DefaultController extends Controller
{

  /**
   * @param Request $request
   *
   * @return RedirectResponse
   */
  public function forwardHome(Request $request)
  {
    return $this->redirect($this->generateUrl('app_default_index', array('_locale' => $request->getLocale())));
  }

  /**
   * @Route("/", defaults={"pageUrl"=""})
   * @Route("/page/{pageUrl}", defaults={"pageUrl"=""}, requirements={"pageUrl"=".+"})
   * @Template
   *
   * @param string          $pageUrl
   *
   * @param RouterInterface $router
   *
   * @return array|RedirectResponse
   */
  public function index(string $pageUrl, RouterInterface $router)
  {
    // Check whether login is required
    if (!$this->isGranted('ROLE_USER')) {
      return $this->redirectToRoute('login');
    }

    return [
        'pageUrl' => $pageUrl != '' ? '/' . $pageUrl : $router->generate('app_wiki_show'),
    ];
  }
}
