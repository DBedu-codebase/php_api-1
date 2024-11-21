<?php

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
// Ensure the correct path and argument types
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();
header("Content-Type: application/json");
class BlogControllers
{
     public function getAllBlog($pdo)
     {
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
     public function postBlog($pdo)
     {
          global $input;
          if (empty($input['title']) || empty($input['content']) || empty($input['category']) || empty($input['tags'])) {
               http_response_code(400);
               echo json_encode(['message' => 'Invalid input']);
               exit();
          }
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
     public function getDetailsBlog($pdo, $id)
     {
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
     }
     public function putDetailsBlog($pdo, $id)
     {
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
     public function deleteBlogId($pdo, $id)
     {
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
};
