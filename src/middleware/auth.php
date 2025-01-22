<?php
require_once './vendor/autoload.php';

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
     private $headers;

     public function __construct()
     {
          $this->headers = getallheaders();
     }

     public function authenticate()
     {
          if (!isset($this->headers['Authorization'])) {
               http_response_code(401);
               echo json_encode(['message' => 'Unauthorized']);
               exit();
          }

          list(, $token) = explode(' ', $this->headers['Authorization'], 2);
          JWT::decode($token, new Key($_ENV['ACCESS_TOKEN_SECRET'], 'HS256'));
     }
}
