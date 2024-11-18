<?php
require_once './vendor/autoload.php';

use Dotenv\Dotenv;

use Firebase\JWT\JWT;

// Ensure the correct path and argument types
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// $dotenv = Dotenv::createImmutable(__DIR__ . '../../'); // Default: looks for `.env` in the current directory
// try {
//      $dotenv->load();
// } catch (\Dotenv\Exception\InvalidPathException $e) {
//      // ...
//      echo $e->getMessage();
// }


// * define input json
$input = json_decode(file_get_contents('php://input'), true);
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
// getRoute('/blog', function () use ($pdo) {
//      return Get_All_Blog($pdo);
// });
// postRoute('/blog', function () use ($pdo) {
//      return Post_Blog($pdo);
// });
// ? auth routes
route('/api/v1/auth/login', function () use ($pdo) {

     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
          http_response_code(405);
          echo json_encode(['message' => 'Method not allowed']);
          exit();
     }

     global $input;
     if (empty($input['email']) || empty($input['password'])) {
          http_response_code(400);
          echo json_encode(['message' => 'Invalid input']);
          exit();
     }

     try {
          $sql = "SELECT * FROM users WHERE email = :email";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([':email' => $input['email']]);
          $user = $stmt->fetch(PDO::FETCH_ASSOC);

          if (!$user || !password_verify($input['password'], $user['password'])) {
               http_response_code(401);
               echo json_encode(['message' => 'Email or password incorrect']);
               exit();
          }

          $expiration_time = time() + 900;
          $payload = [
               'email' => $user['email'],
               'exp' => $expiration_time
          ];

          $access_token = JWT::encode($payload, $_ENV['ACCESS_TOKEN_SECRET'], 'HS256');
          echo json_encode([
               'access_token' => $access_token,
               'expiry' => date(DATE_ATOM, $expiration_time)
          ]);
     } catch (Exception $e) {
          http_response_code(500);
          echo json_encode(['message' => 'Failed to generate token', 'error' => $e->getMessage()]);
     }
});


route('/api/v1/auth/register', function () use ($pdo) {
     // ? create request server by post method 
     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
          http_response_code(405);
          echo json_encode(['message' => 'Method not allowed']);
          exit();
     } else {
          try {
               global $input;
               // * validation simple input
               if (empty($input['email']) || empty($input['name']) || empty($input['password'])) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid input']);
                    exit();
               }
               // * create hash password
               $password = password_hash($input['password'], PASSWORD_DEFAULT);

               $sql = "INSERT INTO users (email,name,password) VALUES (:email,:name,:password)";
               $stmt = $pdo->prepare($sql);
               $stmt->execute([
                    ':email' => $input['email'],
                    ':name' => $input['name'],
                    ':password' => $password,
               ]);
               echo json_encode(['message' => 'User created successfully']);
          } catch (PDOException $e) {
               //throw $th;
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     }
});
// ? end auth routes
// ? blog routes
route('/api/v1/blog/:id', function () {
     //  ? Get details blog, delete blog based on id, update blog based on id
     echo  json_encode(['message' => 'Auth login route']);
});
route('/api/v1/blog', function () use ($pdo) {
     //  ? Get all blog & create a post blog 
     if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SERVER['REQUEST_METHOD'] !== 'GET') {
          http_response_code(405);
          echo json_encode(['message' => 'Method not allowed']);
          exit();
     } else {
          try {
               if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    // * for handle blog request
                    echo json_encode(['message' => 'Get all blog']);
               }
               if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    global $input;
                    try {
                         $sql = "INSERT INTO blog_posts (title, content,category,tags) VALUES (:title,:content,:category,:tags)";
                         $stmt = $pdo->prepare($sql);
                         $stmt->execute([
                              ':title' => $input['title'],
                              ':content' => $input['content'],
                              ':category' => $input['category'],
                              ':tags' => $input['tags'],
                         ]);
                         echo json_encode(['message' => 'Blog post created successfully']);
                    } catch (PDOException $e) {
                         echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
                    }    # code...
               }
          } catch (PDOException $e) {
               //throw $th;
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
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
