<?php

namespace DrensoDeployer;

use DateTimeImmutable;
use Drenso\DeployerBundle\Enum\RunTypeEnum;
use Drenso\DeployerBundle\Scripts\DeploymentScript;

class BuildPythonEnvironment extends DeploymentScript
{
  public function run(): int
  {
    return $this->executeConsoleCommand('ltb:python:build');
  }

  public function timestamp(): DateTimeImmutable
  {
    return new DateTimeImmutable('2026-11-10T21:02:44+00:00');
  }

  public function getRunType(): RunTypeEnum
  {
    return RunTypeEnum::POST;
  }

  public function runOnce(): bool
  {
    return false;
  }
}
