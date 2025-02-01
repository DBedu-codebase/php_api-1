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

     public function getBlogById($id)
     {
          $result = $this->blogModel->getById($id);
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

     // * POST
     public function create()
     {
          try {
               $this->auth->authenticate();
               $input = json_decode(file_get_contents('php://input'), true);
               header("Content-Type: application/json");
               // * validation simple input
               $validationRules = [
                    'title' => 'required|string|min:3|max:50',
                    'content' => 'required|string|min:3|max:255',
               ];
               foreach ($validationRules as $key => $rule) {
                    $this->addRule($key, $rule);
               }

               $errors = $this->validate($input);
               if (!empty($errors)) {
                    http_response_code(400);
                    echo json_encode(['errors' => $errors]);
                    exit();
               }
               // * create blog post
               $result = $this->blogModel->create($input, $this->auth->authenticate());
               echo json_encode([
                    'message' => "Successfully Create Blog Post",
                    'data' => [
                         'blog' => $result
                    ]
               ]);
          } catch (PDOException $e) {
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     }

     // * PUT
     public function update($id)
     {
          try {
               $this->auth->authenticate();
               $Blog = $this->blogModel;
               $getBlogId = $Blog->getById($id);
               if (empty($getBlogId)) {
                    http_response_code(404);
                    echo json_encode([
                         'error' => 'Blog not found',
                    ]);
                    exit();
               }
               $input = json_decode(file_get_contents('php://input'), true);
               header("Content-Type: application/json");
               // * validation simple input
               $validationRules = [
                    'title' => 'required|string|min:3|max:50',
                    'content' => 'required|string|min:3|max:255',
               ];
               foreach ($validationRules as $key => $rule) {
                    $this->addRule($key, $rule);
               }

               $errors = $this->validate($input);
               if (!empty($errors)) {
                    http_response_code(400);
                    echo json_encode(['errors' => $errors]);
                    exit();
               }
               //  * validate author
               $Blog->confirm_author($id, $this->auth->authenticate());
               // * create blog post
               $result = $Blog->update($id, $input, $this->auth->authenticate());
               echo json_encode([
                    'message' => "Successfully Update Blog Post",
                    'data' => [
                         'blog' => $result
                    ]
               ]);
          } catch (PDOException $e) {
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     }

     // * DELETE
     public function delete($id)
     {
          try {
               $this->auth->authenticate();
               $Blog = $this->blogModel;
               $result =  $Blog->getById($id);
               if (empty($result)) {
                    http_response_code(404);
                    echo json_encode([
                         'error' => 'Blog not found',
                    ]);
                    exit();
               }
               //  * validate author
               $Blog->confirm_author($id, $this->auth->authenticate());
               //* Delete blog post
               $Blog->delete($id);
               echo json_encode(['message' => 'Deleted successfully']);
          } catch (PDOException $e) {
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     }
}
