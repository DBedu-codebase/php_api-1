<?php
require_once './vendor/autoload.php';

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
     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
          http_response_code(405);
          echo json_encode(['message' => 'Method not allowed']);
          exit();
     } else {
          try {
               global $input;
               if (empty($input['email']) || empty($input['name']) || empty($input['password'])) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid input']);
                    exit();
               }

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
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     }
});

// blog routes with dynamic :id
route('/api/v1/blog/:id', function ($id) use ($pdo) {
     // Get details of blog, delete or update blog based on the provided id
     if ($_SERVER['REQUEST_METHOD'] === 'GET') {
          // Handle GET request for all blogs
          $headers = getallheaders();
          if (!isset($headers['Authorization'])) {
               http_response_code(401);
               echo json_encode(['message' => 'Unauthorized']);
               exit();
          }
          list(, $token) = explode(' ', $headers['Authorization'], 2);
          JWT::decode($token, new Key($_ENV['ACCESS_TOKEN_SECRET'], 'HS256'));

          $sql = "SELECT * FROM blog_posts WHERE id = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id', $id);
          $stmt->execute();
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          echo $result ? json_encode(['message' => $result]) : json_encode(['message' => 'Blog not found']);
          // echo json_encode(['Get blogs based on id' => $result]);
     }
     if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
          // Handle GET request for all blogs
          $headers = getallheaders();
          if (!isset($headers['Authorization'])) {
               http_response_code(401);
               echo json_encode(['message' => 'Unauthorized']);
               exit();
          }
          list(, $token) = explode(' ', $headers['Authorization'], 2);
          JWT::decode($token, new Key($_ENV['ACCESS_TOKEN_SECRET'], 'HS256'));
          $sql = "DELETE FROM blog_posts WHERE id = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id', $id);
          $stmt->execute();
          echo $stmt->rowCount() > 0 ? json_encode(['message' => 'Blog post deleted successfully']) : json_encode(['message' => 'Blog post could not be deleted']);
     }
     if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
          // Handle PUT request for updating blog
          $headers = getallheaders();

          // Check if Authorization header is set
          if (!isset($headers['Authorization'])) {
               http_response_code(401);
               echo json_encode(['message' => 'Unauthorized']);
               exit();
          }

          // Get the token from the Authorization header
          list(, $token) = explode(' ', $headers['Authorization'], 2);

          try {
               // Decode the token (make sure the JWT class is included)
               JWT::decode($token, new Key($_ENV['ACCESS_TOKEN_SECRET'], 'HS256'));
          } catch (Exception $e) {
               http_response_code(401);
               echo json_encode(['message' => 'Unauthorized']);
               exit();
          }

          // Parse the incoming JSON data (from PUT request body)
          $input = json_decode(file_get_contents('php://input'), true);

          // Validate required fields in the input
          if (empty($input['title']) || empty($input['category']) || empty($input['tags']) || empty($input['content'])) {
               http_response_code(400);
               echo json_encode(['message' => 'Invalid input']);
               exit();
          }

          // Retrieve the blog post ID (it could come from the URL)
          if (!isset($id)) {
               http_response_code(400);
               echo json_encode(['message' => 'Blog post ID is required']);
               exit();
          }

          // $id = $input['id'];

          // Prepare the SQL update query
          $sql = "UPDATE blog_posts SET title = :title, content = :content, category = :category, tags = :tags WHERE id = :id";
          $stmt = $pdo->prepare($sql);

          // Bind parameters and execute the query
          $stmt->bindParam(':id', $id);
          $stmt->bindParam(':title', $input['title']);
          $stmt->bindParam(':content', $input['content']);
          $stmt->bindParam(':category', $input['category']);
          $stmt->bindParam(':tags', $input['tags']);
          $stmt->execute();

          // Return response based on the result of the update
          if ($stmt->rowCount() > 0) {
               echo json_encode(['message' => 'Blog post updated successfully']);
          } else {
               echo json_encode(['message' => 'Blog post could not be updated']);
          }
     }
});

route('/api/v1/blog', function () use ($pdo) {
     if ($_SERVER['REQUEST_METHOD'] === 'GET') {
          // Handle GET request for all blogs
          $headers = getallheaders();
          if (!isset($headers['Authorization'])) {
               http_response_code(401);
               echo json_encode(['message' => 'Unauthorized']);
               exit();
          }
          list(, $token) = explode(' ', $headers['Authorization'], 2);
          JWT::decode($token, new Key($_ENV['ACCESS_TOKEN_SECRET'], 'HS256'));

          $sql = "SELECT * FROM blog_posts";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
          echo json_encode(['blog' => $result]);
     }

     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          global $input;
          try {
               $sql = "INSERT INTO blog_posts (title, content, category, tags) VALUES (:title, :content, :category, :tags)";
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
          }
     }
});

// Define 404 route and run function as previously
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
