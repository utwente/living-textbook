<?php

use App\Kernel;

require __DIR__ . '/../phpunit/bootstrap.php';

$appKernel = new Kernel('test', false);
$appKernel->boot();

return $appKernel->getContainer();
