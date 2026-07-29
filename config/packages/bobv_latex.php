<?php

use Bobv\LatexBundle\Compiler\PodmanProxyLatexCompiler;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
  'bobv_latex' => [
    'compiler' => [
      'class'           => PodmanProxyLatexCompiler::class,
      'dependency_dirs' => [
        'assets/img',
        'uploads',
      ],
    ],
  ],
]);
