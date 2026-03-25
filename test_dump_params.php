<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$request = \Illuminate\Http\Request::create("/oauth/authorize?client_id=1&response_type=code", "GET");
print_r($request->query());
