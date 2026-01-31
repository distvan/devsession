<?php

require_once __DIR__ . '/vendor/autoload.php';

use DevSession\Application;

$app = new Application();
$app->run($argv);

