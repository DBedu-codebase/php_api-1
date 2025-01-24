<?php

namespace App\Middleware;

// Autoload dependencies
require_once './vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DotenvVault\DotenvVault;

// Load environment variables from a .env file
$dotenv = DotenvVault::createImmutable(dirname(__DIR__, 3)); // Go up 3 levels to the project root
$dotenv->safeLoad();

// Define the AuthMiddleware class
class AuthMiddleware
{
    private $headers;

    // Constructor to initialize headers
    public function __construct()
    {
        // Retrieve all HTTP request headers
        $this->headers = getallheaders();
    }

    // Method to authenticate the request
    public function authenticate()
    {
        try {
            // Check if the Authorization header is set
            if (!isset($this->headers['Authorization'])) {
                // Respond with 401 Unauthorized if missing
                http_response_code(401);
                echo json_encode(['message' => 'Unauthorized']);
                exit();
            }

            // Extract the token from the Authorization header
            list(, $token) = explode(' ', $this->headers['Authorization'], 2);
            
            // Decode the JWT token using the secret key
            JWT::decode($token, new Key($_SERVER['ACCESS_TOKEN_SECRET'], 'HS256'));

        } catch (\Throwable $th) {
            // Catch any exceptions and respond with 500 Internal Server Error
            http_response_code(500);
            echo json_encode(['message' => 'Failed to authenticate: ' . $th->getMessage()]);
            exit();
        }
    }
}

