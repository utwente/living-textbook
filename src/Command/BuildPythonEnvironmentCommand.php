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
  /**
   * @var AnalyticsService
   */
  private $analyticsService;

  public function __construct(AnalyticsService $analyticsService)
  {
    parent::__construct(NULL);
    $this->analyticsService = $analyticsService;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $style = new SymfonyStyle($input, $output);
    $this->analyticsService->buildPythonEnvironment($style);
  }
}
