<?php

namespace App\Command;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Routing\Annotation\Route;

class CheckActionSecurityCommand extends Command
{
  /**
   * Makes the command lazy loaded
   *
   * @var string
   */
  protected static $defaultName = 'ltb:check:action-security';

  private $container;

  /**
   * CheckActionSecurityCommand constructor.
   *
   * @param ContainerInterface $container
   */
  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;

    parent::__construct(NULL);
  }

  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this
        ->setDescription('Check if all actions either have a Secure or a PreAuthorize annotation.');
  }

  /**
   * {@inheritdoc}
   * @throws ReflectionException
   * @throws AnnotationException
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    // Initialize variables
    $io                 = new SymfonyStyle($input, $output);
    $noSecurity         = [];
    $checkedControllers = [];
    $annotationReader   = new AnnotationReader();
    // Find all routes
    $routes = $this->container->get('router')->getRouteCollection()->all();

    foreach ($routes as $route => $param) {
      // Get controller string
      $controller = $param->getDefault('_controller');

      if ($controller !== NULL) {
        // Only check own controllers
        if (strpos(strtolower($controller), 'app') === false) continue;

        // Find actions. Possible formats: <service>:<action> and <namespace>:<bundle>:<action>. These need to be checked separately.
        $controllerArray = explode(':', $controller);
        try {
          // Resolve service
          $controllerObject = $this->container->get($controllerArray[0]);
          $action           = $controllerArray[2] ?? $controllerArray[1];
        } catch (ServiceNotFoundException $e) {
          $controllerObject = $controllerArray[0];
          // Merge bundle with namespace, but only if this is defined
          $controllerArray[1] ? $controllerObject .= '/' . $controllerArray[1] : NULL;
          $action = $controllerArray[2];
        }

        // Create ReflectionMethod
        $reflectedMethod = new \ReflectionMethod($controllerObject, $action);
        // Check if Route annotation exists
        if ($annotationReader->getMethodAnnotation($reflectedMethod, Route::class)) {
          // Check if Security or IsGranted annotation exists, if not raise error
          if (!$annotationReader->getMethodAnnotation($reflectedMethod, Security::class) &&
              !$annotationReader->getMethodAnnotation($reflectedMethod, IsGranted::class)) {
            $noSecurity[] = '- ' . $controller;
          }

          // Save as checked for verbose output
          $checkedControllers[] = '- ' . $controller;
        }
      }
    }

    // Build error string
    if (!empty($noSecurity)) {

      $error = [];
      // Concatenate non-pre-authorized methods
      if (!empty($noSecurity)) {
        $error[] = 'The following methods do not contain a Security or IsGranted annotation:';
        $error   = array_merge($error, $noSecurity);
      }

      // Feedback error
      $io->error(implode("\n", $error));

      return 1;
    }

    // No errors occurred!
    $io->success('All methods contain a Security or IsGranted annotation!');

    if ($output->isVerbose()) {
      $output->writeln("Checked controllers:");
      $output->writeln(implode("\n", $checkedControllers));
      $output->writeln('');
    }

    return 0;
  }
}
