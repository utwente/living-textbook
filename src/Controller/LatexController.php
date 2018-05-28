<?php

namespace App\Controller;

use BobV\LatexBundle\Exception\LatexException;
use BobV\LatexBundle\Generator\LatexGeneratorInterface;
use BobV\LatexBundle\Latex\Base\Standalone;
use BobV\LatexBundle\Latex\Element\CustomCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
   * @IsGranted("ROLE_USER")
   *
   * @param Request                 $request
   * @param LatexGeneratorInterface $generator
   *
   * @return Response
   *
   * @throws \Spatie\PdfToImage\Exceptions\InvalidFormat
   * @throws \Spatie\PdfToImage\Exceptions\PdfDoesNotExist
   * @throws \Psr\SimpleCache\InvalidArgumentException
   */
  public function renderLatex(Request $request, LatexGeneratorInterface $generator)
  {
    // Retrieve and check content
    $content = $request->query->get('content', NULL);
    if (!$content) {
      throw $this->createNotFoundException();
    }
    $cacheKey = urlencode($content);

    // Check cache (and whether cached file exists)
    $imageLocation = NULL;
    $cache         = new FilesystemCache('latex.equations', 86400);
    $cached        = true;
    if (NULL === ($imageLocation = $cache->get($cacheKey)) ||
        !(new Filesystem())->exists($imageLocation)) {

      try {
        // Create latex object
        $document = (new Standalone(md5($content)))
            ->addPackages(['mathtools', 'amssymb', 'esint'])
            ->addElement(new CustomCommand('\\begin{displaymath}'))
            ->addElement(new CustomCommand($content))
            ->addElement(new CustomCommand('\\end{displaymath}'));

        // Generate pdf output
        $pdfLocation = $generator->generate($document);

        // Determine output location
        $imageLocation = str_replace('.pdf', '.jpg', $pdfLocation);

        // Convert to image
        $pdf = new Pdf($pdfLocation);
        $pdf->setOutputFormat('jpg');
        $pdf->saveImage($imageLocation);

        // Save location in the cache
        $cache->set($cacheKey, $imageLocation);
      } /** @noinspection PhpRedundantCatchClauseInspection */ catch (LatexException $e) {
        $imageLocation = sprintf('%s/%s',
            $this->getParameter('kernel.project_dir'),
            'public/img/latex/error.jpg');
        $cached        = false;
      }
    }

    // Return image
    $response = new BinaryFileResponse($imageLocation);
    if ($cached) {
      $response->setMaxAge(86400);
      $response->headers->addCacheControlDirective('max-age', 86400);
    }

    return $response;
  }
}
