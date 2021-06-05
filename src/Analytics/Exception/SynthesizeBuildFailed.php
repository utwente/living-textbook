<?php

namespace App\Analytics\Exception;

/**
 * Class SynthesizeBuildFailed
 *
 * Does not extend the ProcessFailedException, as this should not be a RuntimeException
 */
class SynthesizeBuildFailed extends VisualisationBuildFailed
{
}
