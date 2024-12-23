<?php

require_once __DIR__ . "/../vendor/autoload.php";
use Dotenv\Dotenv;

use App\Router\Router;
use App\Middleware\ValidationMiddleware;
use App\Controllers\UserController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$router = new Router();

$router->add('GET', '/', [new App\Controllers\HomeController(), 'index']);
$router->add('POST', '/register', [
  new ValidationMiddleware([
    'name' => 'required',
    'email' => 'required|email|unique:users',
    'password' => 'required|min:6',
  ]),
  [new UserController(), 'register']
]);
$router->add('POST', '/login', [
  new ValidationMiddleware([
    'name' => 'required',
    'email' => 'required|email|unique:users',
    'password' => 'required|min:6',
  ]),
  [new UserController(), 'login']
]);
$router->add('POST', '/logout', [new UserController(), 'logout']);
$router->add('GET', '/user', [new UserController(), 'getUser']);

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->dispatch($method, $uri);

set_exception_handler(['App\Exceptions\ExceptionHandler', 'handle']);