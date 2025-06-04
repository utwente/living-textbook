<?php

namespace App\Analytics;

use App\Analytics\Exception\SynthesizeBuildFailed;
use App\Analytics\Exception\SynthesizeDependenciesFailed;
use App\Analytics\Exception\SynthesizeException;
use App\Analytics\Exception\VisualisationBuildFailed;
use App\Analytics\Exception\VisualisationDependenciesFailed;
use App\Analytics\Exception\VisualisationException;
use App\Analytics\Model\LearningPathVisualisationRequest;
use App\Analytics\Model\LearningPathVisualisationResult;
use App\Analytics\Model\SynthesizeRequest;
use App\Console\NullStyle;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Entity\LearningPath;
use App\Entity\LearningPathElement;
use App\Entity\PageLoad;
use App\Entity\StudyArea;
use App\Excel\SpreadsheetHelper;
use App\Excel\TrackingExportBuilder;
use App\Export\Provider\ConceptIdNameProvider;
use App\Export\Provider\RelationProvider;
use App\Repository\LearningPathRepository;
use App\Repository\PageLoadRepository;
use App\Repository\TrackingEventRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use Symfony\Component\Process\Process;

class AnalyticsService
{
  private const string ENV_DIR = '.venv';

  /** The directory where the python implementation lives   */
  private string $analyticsDir;
  private Filesystem $fileSystem;
  /** The output directory, located in the application cache   */
  private string $baseOutputDir;

  public function __construct(
    private readonly TrackingExportBuilder $trackingExportBuilder,
    private readonly ConceptIdNameProvider $conceptIdNameProvider,
    private readonly SpreadsheetHelper $spreadsheetHelper,
    string $projectDir,
    string $cacheDir,
    private readonly TrackingEventRepository $trackingEventRepository,
    private readonly PageLoadRepository $pageLoadRepository,
    private readonly LearningPathRepository $learningPathRepository,
    private readonly RelationProvider $relationProvider,
    private readonly EntityManagerInterface $entityManager,
    private readonly string $host,
    private readonly bool $isDebug,
    private readonly string $pythonPath)
  {
    $this->analyticsDir  = $projectDir . '/python/data-visualisation';
    $this->baseOutputDir = $cacheDir . '/data-visualisation';
    $this->fileSystem    = new Filesystem();
  }

  /** This function ensures that the python environment is functional. */
  public function ensurePythonEnvironment(?OutputStyle $output): void
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
    new Process([$this->pythonPath, '-m', 'venv', self::ENV_DIR], $this->analyticsDir)
      ->mustRun();

    $progressBar->clear();
    $output->text('Upgrading pip...');
    $progressBar->advance();
    $progressBar->display();

    // Upgrade the pip version in the venv
    Process::fromShellCommandline(
      sprintf('. %s/bin/activate; pip install --upgrade pip', self::ENV_DIR),
      $this->analyticsDir, null, null, 600)
      ->mustRun();

    $progressBar->clear();
    $output->text('Installing python packages...');
    $progressBar->advance();
    $progressBar->display();

    // Install packages
    $process = Process::fromShellCommandline(
      sprintf('. %s/bin/activate; pip install -r requirements.txt --no-cache-dir', self::ENV_DIR),
      $this->analyticsDir, null, null, null);
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

  /** Builds the visualisation for the given learning path */
  public function buildForLearningPath(LearningPathVisualisationRequest $request): LearningPathVisualisationResult
  {
    // Create the settings
    $learningPath = $request->learningPath;
    $settings     = [
      'learningpaths' => [
        $learningPath->getId() => [
          'starting time' => $this->formatPythonDateTime($request->teachingMoment),
          'list'          => $learningPath->getElementsOrdered()->map(fn (LearningPathElement $element) => $element->getConcept()->getId())->toArray(),
          'id'            => $learningPath->getId(),
        ],
      ],
      'functions' => ['usersPerDayPerLearningPath'], // This single function delivers the required output here
    ];

    // Build the visualisation
    $outputDirectory = $this
      ->build($learningPath, $request->periodStart, $request->periodEnd, $settings, $request->forceRebuild);

    // Return the data
    $finder = fn () => new Finder()
      ->files()
      ->in($outputDirectory)
      ->depth(0);

    $result                  = new LearningPathVisualisationResult();
    $result->heatMapImage    = $this->firstFromFinder($finder()->name('heatmap*'));
    $result->pathVisitsImage = $this->firstFromFinder($finder()->name('pathVisits*'));
    $result->pathUsersImage  = $this->firstFromFinder($finder()->name('pathUsers*'));
    $result->flowThroughFile = $this->firstFromFinder($finder()->name('*Flowthrough*'));
    $result->metaDataFile    = $this->firstFromFinder($finder()->name('metaData.json'));

    return $result;
  }

  /**
   * Synthesizes new analytics data for the supplied study area.
   * Any existing data will be purged!
   */
  public function synthesizeDataForStudyArea(StudyArea $studyArea, SynthesizeRequest $request): void
  {
    // Create settings
    $settings = $request->getSettings($this->isDebug, $this->host);

    // Synthesize new data
    // Acquire a lock, only a single build can be run at the same time due to memory constraints
    $lockFactory = new LockFactory(new SemaphoreStore());
    $lock        = $lockFactory->createLock('data-synthesizing');
    $lock->acquire(true);

    // Clear the build dir if it exists
    $buildDir = $this->outputDir($studyArea, 'synthesizing');
    if ($this->fileSystem->exists($buildDir)) {
      $this->fileSystem->remove($buildDir);
    }
    $this->fileSystem->mkdir($buildDir);

    try {
      // Allow for more memory and time usage
      ini_set('memory_limit', '1024M');
      set_time_limit(300);

      // Set output file
      $settings['outputFileName'] = $buildDir . '/synth.csv';

      // Generate learning path data
      $learningPaths = [
        'order' => [],
      ];
      foreach (array_reverse($this->learningPathRepository->findForStudyArea($studyArea)) as $key => $lp) {
        array_unshift($learningPaths['order'], (string)$lp->getId());
        $learningPaths[(string)$lp->getId()] = [
          'lectureMoment' => $request->testMoment
            ->modify(sprintf('-%d days', $request->daysBeforeTest))
            ->modify(sprintf('-%d days', $key * $request->daysBetweenLearningPaths))
            ->format('Y-m-d H:i:s'),
          'concepts'         => array_values(array_map(fn (LearningPathElement $el) => (string)$el->getConcept()->getId(), $lp->getElementsOrdered()->toArray())),
          'learningpathName' => $lp->getName(),
        ];
      }
      $settings['learningpaths'] = $learningPaths;

      // Generate concept data
      $concepts = [];
      foreach ($studyArea->getConcepts() as $concept) {
        $concepts[(string)$concept->getId()] = [
          'timeOnConcept' => random_int(1, 8) * 30,
        ];
      }
      $settings['conceptData'] = $concepts;

      // Retrieve the required input files
      try {
        $settings['conceptFileName'] = $this->retrieveRelationExport($studyArea, $buildDir);

        // Try to free up memory
        gc_collect_cycles();
      } catch (Exception $e) {
        throw new SynthesizeDependenciesFailed('relationData', $e);
      }

      // Write the settings file
      $settingsFile = $buildDir . '/settings.json';
      $this->fileSystem->dumpFile($settingsFile, json_encode($settings));

      // Run the actual build
      $process = Process::fromShellCommandline(
        sprintf('. %s/bin/activate; python SyntheticDataGeneration.py "%s"', self::ENV_DIR, $settingsFile),
        $this->analyticsDir, null, null, 120);
      $process->run();

      if (!$process->isSuccessful()) {
        throw new SynthesizeBuildFailed($process);
      }

      // Try to free up memory
      gc_collect_cycles();
    } catch (Exception $e) {
      // Remove the build directory on errors
      $this->fileSystem->remove($buildDir);

      // Rethrow exception
      if ($e instanceof SynthesizeBuildFailed || $e instanceof SynthesizeDependenciesFailed) {
        throw $e;
      }
      throw new SynthesizeException($e);
    } finally {
      // Release the lock
      $lock->release();
    }

    // Load new data
    $this->entityManager->wrapInTransaction(function () use ($studyArea, &$settings) {
      // Purge existing tracking data
      $this->trackingEventRepository->purgeForStudyArea($studyArea);
      $this->pageLoadRepository->purgeForStudyArea($studyArea);

      // Load new data into db
      $sheet = new Csv()->load($settings['outputFileName'])->getActiveSheet();
      foreach ($sheet->getRowIterator(2) as $i => $row) {
        if ($i % 1000 === 0) {
          $this->entityManager->flush();
          $this->entityManager->clear();

          // Retrieve a new study area reference, as the original object was cleared by the previous call
          $studyArea = $this->entityManager->getReference(StudyArea::class, $studyArea->getId());
        }

        $this->entityManager->persist(
          new PageLoad()
            ->setStudyArea($studyArea)
            ->setUserId($sheet->getCell(CellAddress::fromColumnAndRow(3, $row->getRowIndex()))->getFormattedValue())
            ->setTimestamp(DateTime::createFromFormat('Y-m-d H:i:s',
              $sheet->getCell(CellAddress::fromColumnAndRow(4, $row->getRowIndex()))->getFormattedValue()))
            ->setSessionId($sheet->getCell(CellAddress::fromColumnAndRow(5, $row->getRowIndex()))->getFormattedValue())
            ->setPath($sheet->getCell(CellAddress::fromColumnAndRow(6, $row->getRowIndex()))->getFormattedValue())
            ->setPathContext(unserialize($sheet->getCell(CellAddress::fromColumnAndRow(7, $row->getRowIndex()))->getFormattedValue()))
            ->setOrigin($sheet->getCell(CellAddress::fromColumnAndRow(8, $row->getRowIndex()))->getFormattedValue())
            ->setOriginContext(unserialize($sheet->getCell(CellAddress::fromColumnAndRow(9, $row->getRowIndex()))->getFormattedValue()))
        );
      }

      $this->entityManager->flush();
    });
  }

  /**
   * Builds the visualisation using the supplied parameter file.
   *
   * @return string The output directory, which can be used to load the files
   */
  private function build(
    StudyAreaFilteredInterface $object,
    DateTimeInterface $start,
    DateTimeInterface $end,
    array $settings = [],
    bool $forceBuild = false): string
  {
    // Clear the cache on every invocation
    $this->clearCache();

    // Set some global settings
    $settings['period']       = [
      'usePeriod' => true,
      'startDate' => $this->formatPythonDateTime($start, false),
      'endDate'   => $this->formatPythonDateTime($end, false),
    ];
    $settings['debug']        = $this->isDebug;
    $settings['heatMapColor'] = 'rainbow';

    // Create settings hash for caching
    $settingsHash = md5(serialize($settings));

    // Retrieve the output directory
    $outputDir             = $this->outputDir($object, $settingsHash);
    $buildSuccessFile      = $outputDir . '/build-completed';
    $settings['outputDir'] = $outputDir;

    // Acquire a lock, only a single build can be run at the same time due to memory constraints
    $lockFactory = new LockFactory(new SemaphoreStore());
    $lock        = $lockFactory->createLock('data-visualisation');
    $lock->acquire(true);

    try {
      // If the output directory still exists, no need to rebuild if force is not set
      if (!$forceBuild && $this->fileSystem->exists($outputDir) && $this->fileSystem->exists($buildSuccessFile)) {
        return $outputDir;
      }

      // Remove if the directory exists, which is only the case when forceBuild is set
      if ($this->fileSystem->exists($outputDir)) {
        $this->fileSystem->remove($outputDir);
      }

      // Create output directories
      $this->fileSystem->mkdir($outputDir . '/input');

      // Allow for more memory and time usage
      ini_set('memory_limit', '1024M');
      set_time_limit(300);

      // Retrieve the required input files
      try {
        $settings['dataFilename'] = $this->retrieveTrackingDataExport($object->getStudyArea(), $outputDir);

        // Try to free up memory
        gc_collect_cycles();
      } catch (Exception $e) {
        throw new VisualisationDependenciesFailed('trackingData', $e);
      }
      try {
        $settings['nameFilename'] = $this->retrieveConceptNamesExport($object->getStudyArea(), $outputDir);

        // Try to free up memory
        gc_collect_cycles();
      } catch (Exception $e) {
        throw new VisualisationDependenciesFailed('conceptNames', $e);
      }

      // Write the settings file
      $settingsFile = $outputDir . '/input/settings.json';
      $this->fileSystem->dumpFile($settingsFile, json_encode($settings));

      // Run the actual build
      $process = Process::fromShellCommandline(
        sprintf('. %s/bin/activate; python Main.py "%s"', self::ENV_DIR, $settingsFile),
        $this->analyticsDir, null, null, 120);
      $process->run();

      if (!$process->isSuccessful()) {
        throw new VisualisationBuildFailed($process);
      }

      // Try to free up memory
      gc_collect_cycles();

      // Mark the build as successful
      $this->fileSystem->touch($buildSuccessFile);
    } catch (Exception $e) {
      // Remove the output directory on errors
      $this->fileSystem->remove($outputDir);

      // Rethrow exception
      if ($e instanceof VisualisationDependenciesFailed || $e instanceof VisualisationBuildFailed) {
        throw $e;
      }
      throw new VisualisationException($e);
    } finally {
      // Release the lock
      $lock->release();
    }

    return $outputDir;
  }

  /**
   * Retrieves the tracking data export, and writes it to disk.
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   */
  private function retrieveTrackingDataExport(StudyArea $studyArea, string $outputDir): string
  {
    $fileName = $outputDir . '/tracking_data.xlsx';
    $this->spreadsheetHelper
      ->createExcelWriter($this->trackingExportBuilder->buildSpreadsheet($studyArea))
      ->save($fileName);

    return $fileName;
  }

  /**
   * Retrieves the concept name export, and writes it to disk.
   *
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   */
  private function retrieveConceptNamesExport(StudyArea $studyArea, string $outputDir): string
  {
    $filename = $outputDir . '/concept_names.csv';
    $this->spreadsheetHelper
      ->createCsvWriter($this->conceptIdNameProvider->getSpreadSheet($studyArea))
      ->save($filename);

    return $filename;
  }

  private function retrieveRelationExport(StudyArea $studyArea, string $outputDir): string
  {
    $filename = $outputDir . '/relations.csv';
    $this->spreadsheetHelper
      ->createCsvWriter($this->relationProvider->getSpreadsheet($studyArea))
      ->save($filename);

    return $filename;
  }

  /** Retrieve the output directory */
  private function outputDir(StudyArea|LearningPath|StudyAreaFilteredInterface $object, string $hash): string
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

  /** Cleans the existing cache. All directories older than 1 day are removed */
  private function clearCache(): void
  {
    if (!$this->fileSystem->exists($this->baseOutputDir)) {
      $this->fileSystem->mkdir($this->baseOutputDir);

      return;
    }

    // Find all directories older than one day
    $finder = new Finder()
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
      if ($this->fileSystem->exists($dir->getPathname())) {
        $this->fileSystem->remove($dir->getPathname());
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
