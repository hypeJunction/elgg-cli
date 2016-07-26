<?php

use Elgg\CLI\AddUserCommand;
use Symfony\Component\Console\Application;

require __DIR__.'/vendor/autoload.php';

define('ELGG_APPLICATION_ROOT', dirname(dirname(__FILE__)));

$application = new Application();
$application->add(new AddUserCommand());
$application->run();