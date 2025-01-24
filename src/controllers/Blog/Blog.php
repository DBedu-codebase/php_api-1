<?php

namespace App\Controllers\Blog;

use App\Core\Validation;
use App\Middleware\AuthMiddleware;
use App\Model\BlogModel;

class Blog
{
     public function getAll($pdo)
     {
          $model =  new BlogModel();
          $model->getAll();
          echo json_encode([
               'message' => "Successfully Get All Blog",
               'data' => [
                    'blog' => $model
               ]
          ]);
     }

     public  function getBlogById($pdo, $id)
     {
          $model =  new BlogModel();
          $result = $model->getById($id);
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
          $model =  new BlogModel();
          $result =  $model->getById($id);
          if (empty($result)) {
               http_response_code(404);
               echo json_encode([
                    'error' => 'Blog not found',
               ]);
               exit();
          }
          // Proceed to delete the blog post
          $model->delete($id);
          echo json_encode(['message' => 'Deleted successfully']);
     }
}
