<?php

namespace App\Command;

use App\Repository\StudyAreaRepository;
use App\UrlUtils\UrlChecker;
use Override;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckUrlCommand extends Command
{
  /**
   * Makes the command lazy loaded.
   *
   * @var string
   */
  protected static $defaultName = 'ltb:check:urls';

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
