<?php

namespace App\Analytics;

use App\Console\NullStyle;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Process\Process;

class AnalyticsService
{
  private const ENV_DIR = '.venv';

  /**
   * The directory where the python implementation lives
   *
   * @var string
   */
  private $analyticsDir;

  public function __construct(string $projectDir)
  {
    $this->analyticsDir = $projectDir . '/python/data-visualisation';
  }

  /**
   * This function ensures that the python environment is functional
   *
   * @param OutputStyle|null $output
   */
  public function buildPythonEnvironment(?OutputStyle $output)
  {
    $output = $output ?: new NullStyle(new NullOutput());

    // Create the progress bar
    $progressBar = $output->createProgressBar();
    $progressBar->setFormat(' [%bar%] %memory:6s%');
    $progressBar->start();

    $progressBar->clear();
    $output->text('Creating python virtual environment..');
    $progressBar->display();

    // Create the virtual environment directory
    (new Process(['python3', '-m', 'venv', self::ENV_DIR], $this->analyticsDir))
        ->mustRun();

    $progressBar->clear();
    $output->text('Installing python packages...');
    $progressBar->advance();
    $progressBar->display();

    // Install packages
    $process = Process::fromShellCommandline(
        sprintf('. %s/bin/activate; pip install -r requirements.txt --no-cache-dir', self::ENV_DIR),
        $this->analyticsDir, NULL, NULL, 600);
    $process->mustRun(function ($type, $buffer) use ($output, $progressBar) {
      $progressBar->clear();
      if (Process::ERR === $type) {
        $output->error(trim($buffer));
      } else {
        $output->text(trim($buffer));
        $progressBar->advance();
        $progressBar->display();
      }
    });

    $progressBar->clear();
    $output->writeln('');
  }
}
