<?php
header("Content-Type: application/json");
// ? Model Database connections 
include __DIR__ . '/../model/database.php';
// ? Controllers
require_once __DIR__ . '/../controllers/blog/BlogControllers.php';
// ? Utils
require_once __DIR__ . '/../utils/RoutingHelper.php';
// require_once __DIR__ . '/../utils/RoutingHelper.php';
// Ensure $pdo is accessible
if (!isset($pdo)) {
     echo json_encode(['error' => 'Database connection failed']);
     exit;
}

// Rest of your routing code
$routes = [];

// Define route functions
getRoute('/blog', function () use ($pdo) {
     return Get_All_Blog($pdo);
});
postRoute('/blog', function () use ($pdo) {
     return Post_Blog($pdo);
});
// Define 404 route and run function as previously
route('/404', function () {
     echo json_encode(['error' => 'Page Not Found'], JSON_PRETTY_PRINT);
});

// Routing function
function route(string $path, callable $callback)
{
     global $routes;
     $routes[$path] = $callback;
}

function run()
{
     global $routes, $method;
     $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
     $method = $_SERVER['REQUEST_METHOD'];
     $found = false;

     foreach ($routes as $path => $callback) {
          if ($path !== $uri) continue;

          $found = true;
          $callback();
          break;
     }

     if (!$found) {
          $routes['/404']();
     }
}

run();
