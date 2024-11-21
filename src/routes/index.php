<?php

use App\Controllers\blogTestingControllers;

require './src/controllers/testing_controllers.php';
require_once './src/controllers/auth/AuthControllers.php';
require_once './src/controllers/blog/BlogControllers.php';
require_once './vendor/autoload.php';

// use src\Controllers\Blog\BlogControllers;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Ensure the correct path and argument types
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// * define input json
$input = json_decode(file_get_contents('php://input'), true);
header("Content-Type: application/json");
// ? Model Database connections 
include __DIR__ . '/../model/database.php';
// ? Controllers
require_once __DIR__ . '/../controllers/blog/BlogControllers.php';
// ? Utils
require_once __DIR__ . '/../utils/RoutingHelper.php';
// Ensure $pdo is accessible
if (!isset($pdo)) {
     echo json_encode(['error' => 'Database connection failed']);
     exit;
}

// Rest of your routing code
$routes = [];

// Define route functions
route('/api/v1/auth/login', function () use ($pdo) {
     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
          http_response_code(405);
          echo json_encode(['message' => 'Method not allowed']);
          exit();
     } else {
          try {
               $controller = new AuthUsersControllers();
               $controller->login($pdo);
          } catch (Exception $e) {
               http_response_code(500);
               echo json_encode(['error' => $e->getMessage()]);
          }
     }

     // global $input;
     // if (empty($input['email']) || empty($input['password'])) {
     //      http_response_code(400);
     //      echo json_encode(['message' => 'Invalid input']);
     //      exit();
     // }

});

route('/api/v1/auth/register', function () use ($pdo) {
     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
          http_response_code(405);
          echo json_encode(['message' => 'Method not allowed']);
          exit();
     } else {
          try {
               $controller = new AuthUsersControllers();
               $controller->register($pdo);
          } catch (Exception $e) {
               http_response_code(500);
               echo json_encode(['error' => $e->getMessage()]);
          }

          // try {
          //      global $input;
          //      if (empty($input['email']) || empty($input['name']) || empty($input['password'])) {
          //           http_response_code(400);
          //           echo json_encode(['message' => 'Invalid input']);
          //           exit();
          //      }

          //      $password = password_hash($input['password'], PASSWORD_DEFAULT);

          //      $sql = "INSERT INTO users (email,name,password) VALUES (:email,:name,:password)";
          //      $stmt = $pdo->prepare($sql);
          //      $stmt->execute([
          //           ':email' => $input['email'],
          //           ':name' => $input['name'],
          //           ':password' => $password,
          //      ]);
          //      echo json_encode(['message' => 'User created successfully']);
          // } catch (PDOException $e) {
          //      echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          // }
     }
});

// blog routes with dynamic :id
route('/api/v1/blog/:id', function ($id) use ($pdo) {
     // Get details of blog, delete or update blog based on the provided id
     if ($_SERVER['REQUEST_METHOD'] === 'GET') {
          try {
               $controller = new BlogControllers();
               $controller->getDetailsBlog($pdo, $id);
          } catch (Exception $e) {
               http_response_code(500);
               echo json_encode(['error' => $e->getMessage()]);
          }
          // echo json_encode(['Get blogs based on id' => $result]);
     }
     if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
          // Handle GET request for all blogs
          try {
               $controller = new BlogControllers();
               $controller->deleteBlogId($pdo, $id);
          } catch (Exception $e) {
               http_response_code(500);
               echo json_encode(['error' => $e->getMessage()]);
          }
     }
     if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
          try {
               $controller = new blogControllers();
               $controller->putDetailsBlog($pdo, $id);
          } catch (Exception $e) {
               http_response_code(500);
               echo json_encode(['error' => $e->getMessage()]);
          }
     }
});

route('/api/v1/blog', function () use ($pdo) {
     if ($_SERVER['REQUEST_METHOD'] === 'GET') {
          // Handle GET request for all blogs
          try {
               $controller = new blogControllers();
               $controller->getAllBlog($pdo);
          } catch (Exception $e) {
               http_response_code(500);
               echo json_encode(['error' => $e->getMessage()]);
          }
     }

     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          // Handle POST request for creating a new blog
          try {
               $controller = new blogControllers();
               $controller->postBlog($pdo);
          } catch (Exception $e) {
               http_response_code(500);
               echo json_encode(['error' => $e->getMessage()]);
          }
     }
});

// Define 404 route and run function as previously
route('/test', function () {
     if ($_SERVER['REQUEST_METHOD'] === 'GET') {
          try {
               $controller = new blogTestingControllers();
               $controller->getBlog('test cuy');
          } catch (Exception $e) {
               http_response_code(500);
               echo json_encode(['error' => $e->getMessage()]);
          }
     } else {
          http_response_code(405);
          echo json_encode(['message' => 'Method not allowed']);
     }
});
// route('/auth', function () {
//      if ($_SERVER['REQUEST_METHOD'] === 'GET') {
//           try {
//                $controller = new AuthUsersControllers();
//                $controller->login('test login cuy');
//           } catch (Exception $e) {
//                http_response_code(500);
//                echo json_encode(['error' => $e->getMessage()]);
//           }
//      } else {
//           http_response_code(405);
//           echo json_encode(['message' => 'Method not allowed']);
//      }
// });

route('/404', function () {
     echo json_encode(['error' => 'Page Not Found'], JSON_PRETTY_PRINT);
});

// Routing function to capture dynamic params
function route(string $path, callable $callback)
{
     global $routes;
     $routes[$path] = $callback;
}

// Function to run the routing logic
function run()
{
     global $routes, $method;
     $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
     $method = $_SERVER['REQUEST_METHOD'];
     $found = false;

     foreach ($routes as $path => $callback) {
          $pattern = preg_replace('/\//', '\/', $path); // Escape slashes for regex
          $pattern = preg_replace('/:\w+/', '(\w+)', $pattern); // Capture dynamic params like :id

          // Check if the current URI matches the route pattern
          if (preg_match('/^' . $pattern . '$/', $uri, $matches)) {
               array_shift($matches); // Remove the full match

               // Call the callback with dynamic parameters
               $callback(...$matches);
               $found = true;
               break;
          }
     }

     if (!$found) {
          $routes['/404']();
     }
}

run();
