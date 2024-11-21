<?php

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
// Ensure the correct path and argument types
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
header("Content-Type: application/json");

class AuthMiddleware
{
     public function AuthVerify()
     {
          $headers = getallheaders();
          if (!isset($headers['Authorization'])) {
               http_response_code(401);
               echo json_encode(['message' => 'Unauthorized']);
               exit();
          }
          list(, $token) = explode(' ', $headers['Authorization'], 2);
          try {
               // Decode the token (make sure the JWT class is included)
               JWT::decode($token, new Key($_ENV['ACCESS_TOKEN_SECRET'], 'HS256'));
          } catch (Exception $e) {
               http_response_code(401);
               echo json_encode(['message' => 'Unauthorized']);
               exit();
          }
     }
}
