<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImportTypeDto
{
  public ?string $type = null;

  public ?UploadedFile $json = null;
}
