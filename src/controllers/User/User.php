<?php

namespace App\Controllers\User;


use PDOException;
use Exception;
use Firebase\JWT\JWT;
use App\Core\Validation;
use App\Model\UserModel;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 3), '.env');
$dotenv->load();

class User extends Validation
{
     private UserModel $userModel;

     public function __construct()
     {
          $this->userModel = new UserModel();
     }
     public  function register()
     {
          try {
               $input = json_decode(file_get_contents('php://input'), true);
               header("Content-Type: application/json");
               // * validation simple input

               // * check email and username must be unique

               // * create hash password
          } catch (PDOException $e) {
               //throw $th;
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     }
     public  function login()
     {
          try {
               $input = json_decode(file_get_contents('php://input'), true);
               header("Content-Type: application/json");
               // * validation simple input

               // * check email and password from db

               // * validate password with hash password from db


               // * generate jwt token

          } catch (Exception $e) {
               http_response_code(500);
               echo json_encode(['message' => 'Failed to generate token', 'error' => $e->getMessage()]);
          }
     }
}
