<?php
header("Content-Type: application/json");

// Corrected include path
include __DIR__ . '/../model/database.php';

// Ensure $pdo is accessible
if (!isset($pdo)) {
     echo json_encode(['error' => 'Database connection failed']);
     exit;
}

// Rest of your routing code
$routes = [];

// Define route functions
route('/', function () use ($pdo) {
     global $method;
     if ($method === 'GET') {
          try {
               $sql = "SELECT * FROM blog_posts";
               $stmt = $pdo->prepare($sql);
               $stmt->execute();
               $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
               echo json_encode($result);
          } catch (PDOException $e) {
               echo json_encode(['error' => 'Database error: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     } else {
          echo json_encode(['error' => 'Method Not Allowed'], JSON_PRETTY_PRINT);
     }
});

// Other routes and run function
route('/login', function () {
     global $method;
     if ($method === 'GET') {
          echo json_encode(['message' => 'Login']);
     } else {
          echo json_encode(['error' => 'Method Not Allowed'], JSON_PRETTY_PRINT);
     }
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
