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

  private UrlChecker $urlChecker;

  private StudyAreaRepository $studyAreaRepository;

  /** CheckUrlCommand constructor. */
  public function __construct(UrlChecker $urlChecker, StudyAreaRepository $studyAreaRepository)
  {
    $this->urlChecker          = $urlChecker;
    $this->studyAreaRepository = $studyAreaRepository;
    parent::__construct();
  }

  #[Override]
  protected function configure()
  {
    $this->setDescription('Checks all the URLs in the living textbook to see if there are no dead links.');
  }

  #[Override]
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $this->urlChecker->checkAllUrls(false, false);

    return 0;
  }
}
