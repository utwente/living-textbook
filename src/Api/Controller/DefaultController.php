<?php

namespace App\Api\Controller;

use App\Api\ApiErrorResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractApiController
{
  /**
   * Catch all (configured in routes.yaml) to display a 404 page with user context
   *
   * No IsGranted annotation as this should also work for the dev firewall which has no token
   */
  public function notFound(): JsonResponse
  {
    return new ApiErrorResponse('Not found', Response::HTTP_NOT_FOUND);
  }
}
