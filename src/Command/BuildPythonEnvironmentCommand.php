<?php

namespace App\Command;

use App\Analytics\AnalyticsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('ltb:python:build')]
final readonly class BuildPythonEnvironmentCommand
{
  public function __construct(private AnalyticsService $analyticsService)
  {
  }

  public function __invoke(SymfonyStyle $io): int
  {
    $this->analyticsService->ensurePythonEnvironment($io);

    return Command::SUCCESS;
  }
}
