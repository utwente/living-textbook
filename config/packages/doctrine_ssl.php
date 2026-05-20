<?php

use Pdo\Mysql;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

$connectionOptions = [PDO::ATTR_EMULATE_PREPARES => false];
if (\filter_var($_ENV['SECURED_DB_ENABLED'] ?? false, \FILTER_VALIDATE_BOOL)) {
  $connectionOptions[Mysql::ATTR_SSL_CA]                 = env('SECURED_DB_SSL_CA');
  $connectionOptions[Mysql::ATTR_SSL_VERIFY_SERVER_CERT] = env('SECURED_DB_SSL_VERIFY_SERVER_CERT');
}

return Symfony\Component\DependencyInjection\Loader\Configurator\App::config([
  'parameters' => [
    'env(SECURED_DB_SSL_CA)'                 => '/etc/ssl/certs/ca-certificates.crt',
    'env(SECURED_DB_SSL_VERIFY_SERVER_CERT)' => 'true',
  ],
  'doctrine' => [
    'dbal' => [
      'options' => $connectionOptions,
    ],
  ],
]);
