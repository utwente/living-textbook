<?php

namespace App\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiErrorResponse extends JsonResponse
{
  public function __construct(string $reason, int $status, ?string $description = NULL)
  {
    $data = ['reason' => $reason];
    if ($description) {
      $data['description'] = $description;
    }

    parent::__construct($data, $status);
  }
}
