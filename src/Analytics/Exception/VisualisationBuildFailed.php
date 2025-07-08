<?php

namespace App\Analytics\Exception;

use Exception;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Process;

use function sprintf;

/**
 * Class VisualisationBuildFailed.
 *
 * Does not extend the ProcessFailedException, as this should not be a RuntimeException
 */
class VisualisationBuildFailed extends Exception
{
  public function __construct(Process $process)
  {
    if ($process->isSuccessful()) {
      throw new InvalidArgumentException('Expected a failed process, but the given process was successful.');
    }

    $error = sprintf('The command "%s" failed.' . "\n\nExit Code: %s(%s)\n\nWorking directory: %s",
      $process->getCommandLine(),
      $process->getExitCode(),
      $process->getExitCodeText(),
      $process->getWorkingDirectory()
    );

    if (!$process->isOutputDisabled()) {
      $error .= sprintf("\n\nOutput:\n================\n%s\n\nError Output:\n================\n%s",
        $process->getOutput(),
        $process->getErrorOutput()
      );
    }

    parent::__construct($error);
  }
}
