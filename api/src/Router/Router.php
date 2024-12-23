<?php

namespace App\Router;

class Router
{
  private $routes = [];

  public function add($method, $uri, $handler)
  {
    $this->routes[] = [$method, $uri, $handler];
  }

  public function dispatch($method, $uri)
  {
    foreach ($this->routes as list($routeMethod, $routeUri, $handler)) {
      if ($routeMethod === $method && $routeUri === $uri) {
        if (is_callable($handler)) {
          return call_user_func($handler);
        }
        if (is_array($handler)) {
          return $this->runThroughMiddleware($handler);
        }

        // no match
        header('Content-Type: application/json', true, 404);
        echo json_encode(['error' => 'Not found']);
        return;
      }
    }
    // handle 404 not found
    header('HTTP/1.0 404 Not Found');
    echo json_encode(['error' => 'Not Found']);
  }

  private function runThroughMiddleware(array $stack)
  {
    // We'll pop from the front. The last item is our actual controller action.
    $controllerAction = array_pop($stack);
    $next = function ($request) use (&$stack, $controllerAction, &$next) {
      // if no more middlewares, call the final controller
      if (empty($stack)) {
        return call_user_func($controllerAction, $request);
      }

      // otherwise get the next middleware
      $middleware = array_shift($stack);

      // check if it's an object that implement handle()
      if (method_exists($middleware, 'handle')) {
        return $middleware->handle($request, $next);
      }

      // otherwise, just call it if it's callable
      if (is_callable($middleware)) {
        return call_user_func($middleware, $request, $next);
      }

      throw new \Exception('Invalid middleware.');
    };

    return $next([]);
  }
}
