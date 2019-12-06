<?php

namespace App\Analytics;

use App\Analytics\Exception\VisualisationBuildFailed;
use App\Analytics\Exception\VisualisationDependenciesFailed;
use App\Analytics\Exception\VisualisationException;
use App\Analytics\Model\LearningPathVisualisationRequest;
use App\Analytics\Model\LearningPathVisualisationResult;
use App\Console\NullStyle;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Entity\LearningPath;
use App\Entity\LearningPathElement;
use App\Entity\StudyArea;
use App\Excel\SpreadsheetHelper;
use App\Excel\TrackingExportBuilder;
use App\Export\Provider\ConceptIdNameProvider;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\SemaphoreStore;
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
  /**
   * @var ConceptIdNameProvider
   */
  private $conceptIdNameProvider;
  /**
   * @var Filesystem
   */
  private $fileSystem;
  /**
   * The output directory, located in the application cache
   *
   * @var string
   */
  private $baseOutputDir;
  /**
   * @var SpreadsheetHelper
   */
  private $spreadsheetHelper;
  /**
   * @var TrackingExportBuilder
   */
  private $trackingExportBuilder;

  public function __construct(
      TrackingExportBuilder $trackingExportBuilder, ConceptIdNameProvider $conceptIdNameProvider,
      SpreadsheetHelper $spreadsheetHelper, string $projectDir, string $cacheDir)
  {
    $this->trackingExportBuilder = $trackingExportBuilder;
    $this->conceptIdNameProvider = $conceptIdNameProvider;
    $this->spreadsheetHelper     = $spreadsheetHelper;
    $this->analyticsDir          = $projectDir . '/python/data-visualisation';
    $this->baseOutputDir         = $cacheDir . '/data-visualisation';
    $this->fileSystem            = new Filesystem();
  }

  /**
   * This function ensures that the python environment is functional
   *
   * @param OutputStyle|null $output
   */
  public function ensurePythonEnvironment(?OutputStyle $output)
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

  /**
   * Builds the visualisation for the given learning path
   *
   * @param LearningPathVisualisationRequest $request
   *
   * @return LearningPathVisualisationResult
   *
   * @throws VisualisationBuildFailed
   * @throws VisualisationDependenciesFailed
   */
  public function buildForLearningPath(LearningPathVisualisationRequest $request): LearningPathVisualisationResult
  {
    // Create the settings
    $learningPath = $request->learningPath;
    $settings     = [
        'learningpaths' => [
            $learningPath->getId() => [
                'starting time' => $this->formatPythonDateTime($request->teachingMoment),
                'list'          => $learningPath->getElementsOrdered()->map(function (LearningPathElement $element) {
                  return $element->getConcept()->getId();
                })->toArray(),
                'id'            => $learningPath->getId(),
            ],
        ],
        'functions'     => ['allUsersPerDayPerLearningPath'], // This single function delivers the required output here
    ];

    // Build the visualisation
    $outputDirectory = $this
        ->build($learningPath, $request->periodStart, $request->periodEnd, $settings, $request->forceRebuild);

    // Return the data
    $finder = function () use ($outputDirectory) {
      return (new Finder())
          ->files()
          ->in($outputDirectory)
          ->depth(0);
    };

    $result                  = new LearningPathVisualisationResult();
    $result->heatMapImage    = $this->firstFromFinder($finder()->name('heatmap*'));
    $result->pathVisitsImage = $this->firstFromFinder($finder()->name('pathVisits*'));
    $result->pathUsersImage  = $this->firstFromFinder($finder()->name('pathUsers*'));
    $result->flowThroughFile = $this->firstFromFinder($finder()->name('*Flowthrough*'));
    $result->metaDataFile    = $this->firstFromFinder($finder()->name('metaData.json'));

    return $result;
  }

  /**
   * Builds the visualisation using the supplied parameter file
   *
   * @param StudyAreaFilteredInterface $object
   * @param DateTimeInterface          $start
   * @param DateTimeInterface          $end
   * @param array                      $settings
   * @param bool                       $forceBuild
   *
   * @return string The output directory, which can be used to load the files
   *
   * @throws VisualisationBuildFailed
   * @throws VisualisationDependenciesFailed
   */
  private function build(
      StudyAreaFilteredInterface $object, DateTimeInterface $start, DateTimeInterface $end,
      array $settings = [], bool $forceBuild = false): string
  {
    // Clear the cache on every invocation
    $this->clearCache();

    // Set some global settings
    $settings['period']       = [
        'usePeriod' => false,
        'startDate' => $this->formatPythonDateTime($start, false),
        'endDate'   => $this->formatPythonDateTime($end, false),
    ];
    $settings['debug']        = false;
    $settings['heatMapColor'] = 'rainbow';

    // Create settings hash for caching
    $settingsHash = md5(serialize($settings));

    // Retrieve the output directory
    $outputDir             = $this->outputDir($object, $settingsHash);
    $settings['outputDir'] = $outputDir;

    // Acquire a lock for the current output directory
    $lockFactory = new Factory(new SemaphoreStore());
    $lock        = $lockFactory->createLock('data-visualisation-' . basename($outputDir));
    $lock->acquire(true);

    try {
      // If the output directory still exists, no need to rebuild if force is not set
      if (!$forceBuild && $this->fileSystem->exists($outputDir)) {
        return $outputDir;
      }

      // Remove if the directory exists, which is only the case when forceBuild is set
      if ($this->fileSystem->exists($outputDir)) {
        $this->fileSystem->remove($outputDir);
      }

      // Create output directories
      $this->fileSystem->mkdir($outputDir . '/input');

      // Retrieve the required input files
      try {
        $settings['dataFilename'] = $this->retrieveTrackingDataExport($object->getStudyArea(), $outputDir);
      } catch (Exception $e) {
        throw new VisualisationDependenciesFailed('trackingData', $e);
      }
      try {
        $settings['nameFilename'] = $this->retrieveConceptNamesExport($object->getStudyArea(), $outputDir);
      } catch (Exception $e) {
        throw new VisualisationDependenciesFailed('conceptNames', $e);
      }

      // Write the settings file
      $settingsFile = $outputDir . '/input/settings.json';
      $this->fileSystem->dumpFile($settingsFile, json_encode($settings));

      // Run the actual build
      $process = Process::fromShellCommandline(
          sprintf('. %s/bin/activate; python Main.py "%s"', self::ENV_DIR, $settingsFile),
          $this->analyticsDir, NULL, NULL, 120);
      $process->run();

      if (!$process->isSuccessful()) {
        throw new VisualisationBuildFailed($process);
      }
    } catch (Exception $e) {
      // Remove the output directory on errors
      $this->fileSystem->remove($outputDir);

      // Release the lock
      $lock->release();

      // Rethrow exception
      if ($e instanceof VisualisationDependenciesFailed || $e instanceof VisualisationBuildFailed) {
        throw $e;
      }
      throw new VisualisationException($e);
    }

    return $outputDir;
  }

  /**
   * Retrieves the tracking data export, and writes it to disk
   *
   * @param StudyArea $studyArea
   * @param string    $outputDir
   *
   * @return string
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   */
  private function retrieveTrackingDataExport(StudyArea $studyArea, string $outputDir): string
  {
    $fileName = $outputDir . '/input/tracking_data.xlsx';
    $this->spreadsheetHelper
        ->createExcelWriter($this->trackingExportBuilder->buildSpreadsheet($studyArea))
        ->save($fileName);

    return $fileName;
  }

  /**
   * Retrieves the concept name export, and writes it to disk
   *
   * @param StudyArea $studyArea
   * @param string    $outputDir
   *
   * @return string
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   */
  private function retrieveConceptNamesExport(StudyArea $studyArea, string $outputDir): string
  {
    $filename = $outputDir . '/input/concept_names.csv';
    $this->spreadsheetHelper
        ->createCsvWriter($this->conceptIdNameProvider->getSpreadSheet($studyArea))
        ->save($filename);

    return $filename;
  }

  /**
   * Retrieve the output directory
   *
   * @param StudyAreaFilteredInterface $object
   * @param string                     $hash
   *
   * @return string
   */
  private function outputDir(StudyAreaFilteredInterface $object, string $hash): string
  {
    if ($object instanceof StudyArea) {
      $prefix = 'sa';
    } elseif ($object instanceof LearningPath) {
      $prefix = 'lp';
    } else {
      throw new InvalidArgumentException(
          sprintf('Only %s and %s are supported for analytics', StudyArea::class, LearningPath::class));
    }

    return $this->baseOutputDir . '/' . $prefix . '_' . $object->getId() . '_' . $hash;
  }

  /**
   * Cleans the existing cache. All directories older than 1 day are removed
   */
  private function clearCache(): void
  {
    if (!$this->fileSystem->exists($this->baseOutputDir)) {
      $this->fileSystem->mkdir($this->baseOutputDir);

      return;
    }

    // Find all directories older than one day
    $finder = (new Finder())
        ->directories()
        ->in($this->baseOutputDir)
        ->depth('== 0')
        ->date('before today');

    // Early exit when no directories match
    if (!$finder->hasResults()) {
      return;
    }

    // Remove the matched directories
    foreach ($finder as $dir) {
      if ($this->fileSystem->exists($dir)) {
        $this->fileSystem->remove($dir);
      }
    }
  }

  private function formatPythonDateTime(DateTimeInterface $dateTime, bool $includeTime = true): string
  {
    return $includeTime
        ? $dateTime->format('Y-m-d H:i:s')
        : $dateTime->format('Y-m-d');
  }

  private function firstFromFinder(Finder $finder)
  {
    $iterator = $finder->getIterator();
    $iterator->rewind();

    return iterator_to_array($iterator, false)[0];
  }
}
