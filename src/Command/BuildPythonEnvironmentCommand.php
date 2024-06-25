<?php

namespace App\Command;

use App\Analytics\AnalyticsService;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('ltb:python:build')]
class BuildPythonEnvironmentCommand extends Command
{
  public function __construct(private readonly AnalyticsService $analyticsService)
  {
    parent::__construct();
  }

  #[Override]
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $style = new SymfonyStyle($input, $output);
    $this->analyticsService->ensurePythonEnvironment($style);

    return Command::SUCCESS;
  }
}
