<?php

namespace App\Core;

class App
{
     private array $routes = [];

     public function get(string $path, callable|array $callback): void
     {
          $this->addRoute('GET', $path, $callback);
     }

     public function post(string $path, callable|array $callback): void
     {
          $this->addRoute('POST', $path, $callback);
     }

     public function put(string $path, callable|array $callback): void
     {
          $this->addRoute('PUT', $path, $callback);
     }

     public function delete(string $path, callable|array $callback): void
     {
          $this->addRoute('DELETE', $path, $callback);
     }

     private function addRoute(string $method, string $path, callable|array $callback): void
     {
          $this->routes[] = [
               'method' => $method,
               'path' => $path,
               'callback' => $callback,
          ];
     }

     public function run(): void
     {
          $requestMethod = $_SERVER['REQUEST_METHOD'];
          $requestUri = strtok($_SERVER['REQUEST_URI'], '?'); // Strip query parameters
          $params = []; // Initialize the params array

          foreach ($this->routes as $route) {
               if ($requestMethod === $route['method'] && $this->match($route['path'], $requestUri, $params)) {
                    if (is_callable($route['callback'])) {
                         call_user_func_array($route['callback'], $params);
                    } elseif (is_array($route['callback']) && count($route['callback']) === 2) {
                         call_user_func([$route['callback'][0], $route['callback'][1]], ...$params);
                    }
                    return;
               }
          }

          http_response_code(404);
          echo json_encode(['error' => 'Route not found']);
          exit;
     }

     private function match(string $routePath, string $requestUri, array &$params): bool
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

     public function render(string $view, array $data = []): void
     {
          extract($data); // Extract variables into the current scope
          $viewPath = __DIR__ . '/../views/' . $view . '.php';

          if (file_exists($viewPath)) {
               include $viewPath; // Include the file only if it exists
          } else {
               echo "View not found: " . $view;
          }
     }
}
