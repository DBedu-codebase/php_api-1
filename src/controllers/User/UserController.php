<?php

namespace App\Controllers;

require_once './vendor/autoload.php';


use PDOException;

class UserControllers
{
     public static function register($pdo)
     {
          try {
               global $input;
               // * validation simple input
               if (empty($input['email']) || empty($input['username']) || empty($input['password_hash'])) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid input']);
                    exit();
               }
               // * check email and username must be unique
               $sql = "SELECT email,username FROM user WHERE email = :email OR username = :username";
               $stmt = $pdo->prepare($sql);
               $stmt->execute([
                    ':email' => $input['email'],
                    ':username' => $input['username'],
               ]);
               $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
               if (!empty($result)) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Email or username already exists']);
                    exit();
               }
               // * create hash password
               $password = password_hash($input['password_hash'], PASSWORD_DEFAULT);

               $sql = "INSERT INTO user (email,username,password_hash) VALUES (:email,:username,:password_hash)";
               $stmt = $pdo->prepare($sql);
               $stmt->execute([
                    ':email' => $input['email'],
                    ':username' => $input['username'],
                    ':password_hash' => $password,
               ]);
               unset($input['password_hash']);
               echo json_encode([
                    'message' => 'User created successfully',
                    'data' => [
                         'user' => $input
                    ]
               ]);
          } catch (PDOException $e) {
               //throw $th;
               echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
          }
     }
}
