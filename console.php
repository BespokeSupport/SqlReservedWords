<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new \BespokeSupport\SqlReserved\CheckCommand());

try {
    $application->run();
} catch (Exception $e) {
    echo $e->getLine();
    echo PHP_EOL;
    echo $e->getMessage();
    echo PHP_EOL;
    echo $e->getTraceAsString();
}
