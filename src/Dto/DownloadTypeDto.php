<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class DownloadTypeDto
{
  /** @var string[] */
  public const array METHODS         = [Request::METHOD_POST, Request::METHOD_PUT];
  public const string GROUP_DOWNLOAD = 'download';
  public const string GROUP_EXPORT   = 'export';

  public function __construct(
    #[Assert\Length(min: 1, groups: [self::GROUP_DOWNLOAD, self::GROUP_EXPORT])]
    public string $type = '',

    #[Assert\NotNull(groups: [self::GROUP_EXPORT])]
    #[Assert\Length(max: 512)]
    #[Assert\Url(groups: [self::GROUP_EXPORT])]
    public ?string $exportUrl = null,

    #[Assert\Choice(choices: self::METHODS, groups: [self::GROUP_EXPORT])]
    public string $httpMethod = Request::METHOD_PUT, // Default to PUT
  ) {
  }
}
