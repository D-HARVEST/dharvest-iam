<?php

use Spiral\RoadRunner\Worker;
use Spiral\RoadRunner\GRPC\Server;
use App\Services\AuthServiceHandler;

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$worker = Worker::create();
$server = new Server();

// Register the service
$server->registerService(\App\Grpc\Auth\AuthServiceInterface::class, new AuthServiceHandler());

// Run the server
$server->serve($worker);
