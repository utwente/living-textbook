<?php

namespace App\Controller;

use App\Entity\Abbreviation;
use App\Entity\Concept;
use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRepository;
use App\Request\Wrapper\RequestStudyArea;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class SearchController
 *
 * @Route("/{_studyArea}/search", requirements={"_studyArea"="\d+"})
 */
class SearchController extends Controller
{
  /**
   * @Template()
   *
   * @param FormFactoryInterface $formFactory
   * @param TranslatorInterface  $translator
   *
   * @return array
   */
  public function searchForm(FormFactoryInterface $formFactory, TranslatorInterface $translator)
  {
    return [
        'form' => $this->createSearchForm($formFactory, $translator, false)->createView(),
    ];
  }

  /**
   * @param FormFactoryInterface $formFactory
   * @param TranslatorInterface  $translator
   * @param mixed                $label
   *
   * @return \Symfony\Component\Form\FormInterface
   */
  private function createSearchForm(FormFactoryInterface $formFactory, TranslatorInterface $translator, $label = 'search.search'): \Symfony\Component\Form\FormInterface
  {
    return $formFactory->createNamedBuilder('search_form')
        ->setAction($this->generateUrl('search'))
        ->add('s', TextType::class, [
            'label'       => $label,
            'hide_label'  => $label === false,
            'attr'        => [
                'placeholder' => $translator->trans('search.placeholder'),
            ],
            'constraints' => [
                new Length([
                    'min' => 3,
                    'max' => 100,
                ]),
            ],
        ])
        ->getForm();
  }

}
