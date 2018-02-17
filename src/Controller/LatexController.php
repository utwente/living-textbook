<?php

namespace App\Controller;

use BobV\LatexBundle\Generator\LatexGeneratorInterface;
use BobV\LatexBundle\Latex\Base\Standalone;
use BobV\LatexBundle\Latex\Element\CustomCommand;
use Spatie\PdfToImage\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LatexController
 *
 * @author BobV
 *
 * @Route("/latex")
 */
class LatexController extends Controller
{

  /**
   * @Route("/render", methods={"GET"}, options={"expose"=true})
   *
   * @param Request                 $request
   * @param LatexGeneratorInterface $generator
   *
   * @return Response
   *
   * @throws \BobV\LatexBundle\Exception\LatexException
   * @throws \Spatie\PdfToImage\Exceptions\InvalidFormat
   * @throws \Spatie\PdfToImage\Exceptions\PdfDoesNotExist
   */
  public function renderLatex(Request $request, LatexGeneratorInterface $generator)
  {
    // Retrieve and check content
    $content = $request->query->get('content', NULL);
    if (!$content) {
      throw $this->createNotFoundException();
    }

    // Check cache (and whether cached file exists)
    $imageLocation = NULL;
    $cache         = new FilesystemCache('latex.equations', 86400);
    if (NULL === ($imageLocation = $cache->get($content)) ||
        !(new Filesystem())->exists($imageLocation)) {

      // Create latex object
      $document = (new Standalone(md5($content)))
          ->addElement(new CustomCommand($content));

      // Generate pdf output
      $pdfLocation = $generator->generate($document);

      // Determine output location
      $imageLocation = str_replace('.pdf', '.jpg', $pdfLocation);

      // Convert to image
      $pdf = new Pdf($pdfLocation);
      $pdf->setOutputFormat('jpg');
      $pdf->saveImage($imageLocation);

      // Save location in the cache
      $cache->set($content, $imageLocation);
    }

    // Return image
    return new BinaryFileResponse($imageLocation);
  }
}
