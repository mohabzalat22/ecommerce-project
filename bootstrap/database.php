<?php

declare(strict_types=1);

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;

$databaseConfig = require BASE_PATH.'/config/database.php';
$defaultConnection = $databaseConfig['default'] ?? 'mysql';
$connection = $databaseConfig['connections'][$defaultConnection] ?? [];

$capsule = new Capsule();
$capsule->addConnection($connection);

// Optional but recommended
$capsule->setEventDispatcher(
    new Dispatcher(new Container())
);

// Make globally accessible
$capsule->setAsGlobal();

// Boot Eloquent
$capsule->bootEloquent();
