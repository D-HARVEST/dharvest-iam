<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$request = \Illuminate\Http\Request::create('/oauth/authorize', 'GET', ['client_id'=>'123']);
$request->setRouteResolver(function() {
    $route = new \Illuminate\Routing\Route('GET', '/oauth/authorize', []);
    $route->name('passport.authorizations.authorize');
    return $route;
});

app()->instance('request', $request);

$exception = new \Illuminate\Auth\AuthenticationException("unauthenticated");
echo "Result: " . $exception->redirectTo($request) . "\n";
echo "Session oauth_params: " . json_encode(session('oauth_params')) . "\n";
