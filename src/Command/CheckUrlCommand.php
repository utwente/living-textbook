<?php

namespace App\Command;

use App\Entity\StudyArea;
use App\Repository\StudyAreaRepository;
use App\UrlUtils\Model\Url;
use App\UrlUtils\UrlChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckUrlCommand extends Command
{

  /**
   * Makes the command lazy loaded
   *
   * @var string
   */
  protected static $defaultName = 'ltb:check:urls';

  /**
   * @var UrlChecker
   */
  private $urlChecker;

  /**
   * @var StudyAreaRepository
   */
  private $studyAreaRepository;

  /**
   * CheckUrlCommand constructor.
   *
   * @param UrlChecker          $urlChecker
   * @param StudyAreaRepository $studyAreaRepository
   */
  public function __construct(UrlChecker $urlChecker, StudyAreaRepository $studyAreaRepository)
  {
    $this->urlChecker          = $urlChecker;
    $this->studyAreaRepository = $studyAreaRepository;
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this->setDescription('Checks all the URLs in the living textbook to see if there are no dead links.');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $urls = $this->urlChecker->checkAllUrls();
    foreach ($urls as $id => $studyAreaUrls) {
      foreach ($studyAreaUrls as $name => $childUrls) {
        foreach ($childUrls as $childUrl) {
          assert($childUrl instanceof Url);
          $studyArea = $this->studyAreaRepository->find($id);
          assert($studyArea instanceof StudyArea);
          echo($childUrl->getUrl() . ' in ' . $childUrl->getContext()->getClass() . ' called ' . $name . ' property ' . $childUrl->getContext()->getPath() . ' in study area ' . $studyArea->getName() . ' owned by ' . $studyArea->getOwner()->getFullName() . "\n");
        }
      }
    }
  }
}