<?php

return Symfony\Component\DependencyInjection\Loader\Configurator\App::config([
  'drenso_deployer' => [
    'generator_enabled' => false,
    'update_pages'      => [
      'enabled'            => false,
      'preview_controller' => true,
      'configurations'     => [
        'update' => [
          'page_title'       => 'Living Textbook | Home | By ITC, University of Twente',
          'background_color' => '#E4E4E4',
          'svg'              => '%kernel.project_dir%/assets/img/icon/LTB_Logo_V4.svg',
        ],
      ],
    ],
  ],
  'when@dev' => [
    'drenso_deployer' => [
      'generator_enabled' => true,
      'update_pages'      => ['enabled' => true],
    ],
  ],
]);
