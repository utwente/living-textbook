<?php

namespace App\Command;

use App\UrlUtils\UrlChecker;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('ltb:check:urls')]
class CheckUrlCommand extends Command
{
  public function __construct(private readonly UrlChecker $urlChecker)
  {
    parent::__construct();
  }

  #[Override]
  protected function configure(): void
  {
    $this->setDescription('Checks all the URLs in the living textbook to see if there are no dead links.');
  }

  #[Override]
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $this->urlChecker->checkAllUrls(false, false);

    return Command::SUCCESS;
  }
}
