<?php

namespace App\Command;

use App\Analytics\AnalyticsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BuildPythonEnvironmentCommand extends Command
{
  /**
   * Makes the command lazy loaded
   *
   * @var string
   */
  protected static $defaultName = 'ltb:python:build';

  public function __construct(private AnalyticsService $analyticsService)
  {
    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $style = new SymfonyStyle($input, $output);
    $this->analyticsService->ensurePythonEnvironment($style);

    return 0;
  }
}
