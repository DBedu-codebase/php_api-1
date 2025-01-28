<?php

namespace App\Controllers\User;

use Dotenv\Validator;
use PDOException;
use Exception;
use Firebase\JWT\JWT;
use DotenvVault\DotenvVault;
use App\Core\Validation;
use App\Model\UserModel;

$dotenv = DotenvVault::createImmutable(dirname(__DIR__, 3)); // Go up 3 levels to the project root
$dotenv->safeLoad();

class User extends Validation
{
     public  function register($pdo)
     {
          try {
               $user = new UserModel();
               $input = json_decode(file_get_contents('php://input'), true);
               header("Content-Type: application/json");
               // * validation simple input
               $this->addRule('email', 'email|required');
               $this->addRule('username', 'string|required|min:3|max:50');
               $this->addRule('password_hash', 'password|required|min:8|max:255');

               $errors = $this->validate($input);
               if (!empty($errors)) {
                    http_response_code(400);
                    echo json_encode(['errors' => $errors]);
                    exit();
               }
               // * check email and username must be unique
               $result = $user->getUniqueUser($input['email'], $input['username']);
               if (!empty($result)) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Email or username already exists']);
                    exit();
               }
               // * create hash password
               $password = password_hash($input['password_hash'], PASSWORD_DEFAULT);
               echo $user->PostUniqueUser($input['email'], $input['username'], $password);
          } catch (PDOException $e) {
               //throw $th;
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     }
     public  function login($pdo)
     {
          try {
               $user = new UserModel();
               $input = json_decode(file_get_contents('php://input'), true);
               header("Content-Type: application/json");
               // * validation simple input
               if (empty($input['email']) || empty($input['password_hash'])) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid input']);
                    exit();
               }
               // * check email and password from db

               $result = $user->getUniqueUserEmail($input['email']);
               if (!$result) {
                    http_response_code(401);
                    echo json_encode(['message' => 'Email or password incorrect']);
                    exit();
               }
               // * validate password with hash password from db
               if (!password_verify($input['password_hash'], $result['password_hash'])) {
                    http_response_code(401);
                    echo json_encode(['message' => 'Email or password incorrect']);
                    exit();
               }
               // * generate jwt token
               $expiration_time = time() + 900;
               $payload = [
                    'email' => $result['email'],
                    'exp' => $expiration_time
               ];

               if (!$_SERVER['ACCESS_TOKEN_SECRET']) {
                    http_response_code(500);
                    echo json_encode(['message' => 'Failed to generate token', 'error' => 'ACCESS_TOKEN_SECRET is not set']);
                    exit();
               }

               $access_token = JWT::encode($payload, $_SERVER['ACCESS_TOKEN_SECRET'], 'HS256');

               echo json_encode([
                    'message' => 'Login successfully',
                    'data' => [
                         'access_token' => $access_token,
                         'expiry' => date(DATE_ATOM, $expiration_time)
                    ]
               ]);
          } catch (Exception $e) {
               http_response_code(500);
               echo json_encode(['message' => 'Failed to generate token', 'error' => $e->getMessage()]);
          }
     }
}
