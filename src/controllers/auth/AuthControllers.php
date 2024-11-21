<?php

use Dotenv\Dotenv;
use Firebase\JWT\JWT;

// Ensure the correct path and argument types
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();
header("Content-Type: application/json");
class AuthUsersControllers
{
     public function register($pdo)
     {
          global $input;
          try {
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
     public function login($pdo)
     {
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
     }
};
