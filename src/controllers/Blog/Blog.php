<?php

namespace App\Controllers\Blog;

use App\Core\Validation;
use App\Middleware\Auth;
use App\Model\BlogModel;
use PDOException;

class Blog extends Validation
{
     private Auth $auth;
     private BlogModel $blogModel;
     public function __construct()
     {
          $this->auth = new Auth();
          $this->blogModel = new BlogModel();
     }

     // * GET
     public function getAll()
     {
          $result = $this->blogModel->getAll();
          echo json_encode([
               'message' => "Successfully Get All Blog",
               'data' => [
                    'blog' => $result,
                    'payload' => $this->auth->authenticate()
               ]
          ]);
     }

     public function getBlogById($id) {}

     // * POST
     public function create()
     {
          try {
               $input = json_decode(file_get_contents('php://input'), true);
               header("Content-Type: application/json");
               // * validation simple input
               // * create blog post
          } catch (PDOException $e) {
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     }

     // * PUT
     public function update($id)
     {
          try {
               $input = json_decode(file_get_contents('php://input'), true);
               header("Content-Type: application/json");
               // * validation simple input

               //  * validate author

               // * create blog post


          } catch (PDOException $e) {
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     }

     // * DELETE
     public function delete($id)
     {
          try {

               //  * validate author

               //* Delete blog post

          } catch (PDOException $e) {
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     }
}
