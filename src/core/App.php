<?php

namespace App\Core;

class App
{
     private $routes = [];

     public function get($path, $callback)
     {
          $this->addRoute('GET', $path, $callback);
     }

     public function post($path, $callback)
     {
          $this->addRoute('POST', $path, $callback);
     }

     public function put($path, $callback)
     {
          $this->addRoute('PUT', $path, $callback);
     }

     public function delete($path, $callback)
     {
          $this->addRoute('DELETE', $path, $callback);
     }

     private function addRoute($method, $path, $callback)
     {
          $this->routes[] = [
               'method' => $method,
               'path' => $path,
               'callback' => $callback,
          ];
     }

     public function run()
     {
          $requestMethod = $_SERVER['REQUEST_METHOD'];
          $requestUri = strtok($_SERVER['REQUEST_URI'], '?'); // Strip query parameters

          foreach ($this->routes as $route) {
               if ($requestMethod === $route['method'] && $this->match($route['path'], $requestUri, $params)) {
                    if (is_callable($route['callback'])) {
                         call_user_func_array($route['callback'], $params);
                    } else {
                         call_user_func([$route['callback'][0], $route['callback'][1]], ...$params);
                    }
                    return;
               }
          }

          http_response_code(404);
          echo json_encode(['error' => 'Route not found']);
          exit;
     }

     private function match($routePath, $requestUri, &$params)
     {
          $routeRegex = preg_replace('#:([\w]+)#', '([\w-]+)', $routePath);
          $routeRegex = '#^' . $routeRegex . '$#';

          if (preg_match($routeRegex, $requestUri, $matches)) {
               array_shift($matches); // Remove full match
               $params = $matches;
               return true;
          }

          return false;
     }
}
