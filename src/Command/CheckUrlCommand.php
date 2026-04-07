<?php

namespace App\Command;

use App\UrlUtils\UrlChecker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand('ltb:check:urls', description: 'Checks all the URLs in the living textbook to see if there are no dead links.')]
final readonly class CheckUrlCommand
{
  public function __construct(private UrlChecker $urlChecker)
  {
  }

  public function __invoke(): int
  {
    $this->urlChecker->checkAllUrls(false, false);

    return Command::SUCCESS;
  }
}
