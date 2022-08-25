<?php

namespace App\Controller;

use BobV\LatexBundle\Generator\LatexGeneratorInterface;
use BobV\LatexBundle\Latex\Base\Standalone;
use BobV\LatexBundle\Latex\Element\CustomCommand;
use DateTime;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Spatie\PdfToImage\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class LatexController.
 *
 * @author BobV
 *
 * @Route("/latex")
 */
class LatexController extends AbstractController
{
  /**
   * @Route("/render", methods={"GET"}, options={"expose"=true,"no_login_wrap"=true})
   * @IsGranted("PUBLIC_ACCESS")
   *
   * @throws InvalidArgumentException
   *
   * @return Response
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function renderLatex(Request $request, LatexGeneratorInterface $generator)
  {
    // Retrieve and check content
    $content = $request->query->get('content', null);
    if (!$content) {
      throw $this->createNotFoundException();
    }
    $cacheKey = urlencode($content);

    // Check cache (and whether cached file exists)
    $imageLocation = null;
    $cache         = new FilesystemAdapter('latex.equations', 86400);
    $cached        = true;

    // Verify image still exists in cache
    if ($cache->hasItem($cacheKey)) {
      $imageLocation = $cache->getItem($cacheKey)->get();
      if (!(new Filesystem())->exists($imageLocation)) {
        $cache->delete($cacheKey);
        $imageLocation = null;
      }
    }

    $imageLocation = $cache->get($cacheKey, function (ItemInterface $item) use ($content, $generator, &$cached) {
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
      } catch (Exception) {
        $imageLocation = sprintf('%s/%s',
            $this->getParameter('kernel.project_dir'),
            'public/img/latex/error.jpg');

        // Do not really store it in the cache
        $item->expiresAfter(0);
        $cached = false;
      }

      // Save location in the cache
      return $imageLocation;
    });

    // Return image
    $response = $this->file($imageLocation, null, ResponseHeaderBag::DISPOSITION_INLINE);
    if ($cached) {
      // Disable symfony's automatic cache control header
      /* @phan-suppress-next-line PhanAccessClassConstantInternal */
      $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');

      // Setup cache headers
      $response->setLastModified(DateTime::createFromFormat('U', (string)filemtime($imageLocation)));
      $response->setAutoEtag();
      $response->setMaxAge(604800); // One week
      $response->setPrivate();

      // Check if response was cached: if so, the content is automatically purged
      $response->isNotModified($request);
    }

    return $response;
  }
}
