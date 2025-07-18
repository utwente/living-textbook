<?php

namespace App\Controller;

use Bobv\LatexBundle\Generator\LatexGeneratorInterface;
use Bobv\LatexBundle\Latex\Base\Standalone;
use Bobv\LatexBundle\Latex\Element\CustomCommand;
use DateTime;
use Drenso\PdfToImage\Pdf;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\Exception\RateLimitExceededException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\ItemInterface;

use function filemtime;
use function md5;
use function sprintf;
use function str_replace;
use function time;
use function urlencode;

#[Route('/latex')]
class LatexController extends AbstractController
{
  /** @throws InvalidArgumentException */
  #[Route('/render', options: ['expose' => true, 'no_login_wrap' => true], methods: [Request::METHOD_GET])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function renderLatex(
    Request $request,
    LatexGeneratorInterface $generator,
    RateLimiterFactory $latexGeneratorLimiter): Response
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
      if (!new Filesystem()->exists($imageLocation)) {
        $cache->delete($cacheKey);
        $imageLocation = null;
      }
    }

    $imageLocation = $cache->get($cacheKey, function (ItemInterface $item) use (
      $content,
      $generator,
      &$cached,
      $request,
      $latexGeneratorLimiter,
    ) {
      try {
        if (!$this->isGranted('ROLE_USER')) {
          // Enforce generation limit
          $latexGeneratorLimiter
            ->create($request->getClientIp())
            ->consume()
            ->ensureAccepted();
        }

        // Create latex object
        $document = new Standalone(md5($content))
          ->addPackages(['mathtools', 'amssymb', 'esint'])
          ->addElement(new CustomCommand('\\begin{displaymath}'))
          ->addElement(new CustomCommand($content))
          ->addElement(new CustomCommand('\\end{displaymath}'));

        // Generate pdf output
        $pdfLocation = $generator->generate($document);

        // Determine output location
        $imageLocation = str_replace('.pdf', '.png', $pdfLocation);

        // Convert to image
        $pdf = new Pdf($pdfLocation);
        $pdf->saveImage($imageLocation);
      } catch (RateLimitExceededException $e) {
        $limit = $e->getRateLimit();

        $retryAfter = $limit->getRetryAfter()->getTimestamp() - time();
        throw new TooManyRequestsHttpException($retryAfter, headers: [
          'X-RateLimit-Remaining'   => $limit->getRemainingTokens(),
          'X-RateLimit-Retry-After' => $retryAfter,
          'X-RateLimit-Limit'       => $limit->getLimit(),
        ]);
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
