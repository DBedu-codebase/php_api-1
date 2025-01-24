<?php

namespace App\Controllers;

require_once './vendor/autoload.php';

use App\Middleware\AuthMiddleware;

require_once './src/middleware/auth.php';
class BlogControllers
{
     public static function getAll($pdo)
     {
          $auth = new AuthMiddleware();
          $auth->authenticate();
          $sql = "SELECT * FROM blog";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
          echo json_encode([
               'message' => "Successfully Get All Blog",
               'data' => [
                    'blog' => $result
               ]
          ]);
     }

     public static function getBlogById($pdo, $id)
     {
          $auth = new AuthMiddleware();
          $auth->authenticate();
          $sql = "SELECT * FROM blog WHERE post_id = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([':id' => $id]);
          $result = $stmt->fetch(\PDO::FETCH_ASSOC);

          if (empty($result)) {
               http_response_code(404);
               echo json_encode([
                    'error' => 'Blog not found',
               ]);
               exit();
          }

          echo json_encode([
               'message' => "Successfully Get Blog Based Id",
               'data' => [
                    'blog' => $result
               ]
          ]);
     }
     public static function delete($pdo, $id)
     {
          $auth = new AuthMiddleware();
          $auth->authenticate();
          // Validate the ID
          if (empty($id) || !is_numeric($id)) {
               http_response_code(400);
               echo json_encode(['error' => 'Invalid ID']);
               exit();
          }

          // Check if the blog post exists
          $checkSql = "SELECT * FROM blog WHERE post_id = :id";
          $checkStmt = $pdo->prepare($checkSql);
          $checkStmt->execute([':id' => $id]);
          $result = $checkStmt->fetch(\PDO::FETCH_ASSOC);

          if (!$result) {
               http_response_code(404);
               echo json_encode(['error' => 'Blog post not found']);
               exit();
          }

          // Proceed to delete the blog post
          $sql = "DELETE FROM blog WHERE post_id = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([':id' => $id]);

          echo json_encode(['message' => 'Deleted successfully']);
     }
}
