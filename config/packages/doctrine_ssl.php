<?php

use Pdo\Mysql;

$connectionOptions = [PDO::ATTR_EMULATE_PREPARES => false];
if (\filter_var($_ENV['SECURED_DB_CONNECTION'], \FILTER_VALIDATE_BOOL)) {
  $connectionOptions[Mysql::ATTR_SSL_CA]                 = '/etc/certs/mysql/ca-cert.pem';
  $connectionOptions[Mysql::ATTR_SSL_VERIFY_SERVER_CERT] = 1;
}

return Symfony\Component\DependencyInjection\Loader\Configurator\App::config([
  'doctrine' => [
    'dbal' => [
      'options' => $connectionOptions,
    ],
  ],
]);
