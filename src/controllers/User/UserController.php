<?php

require_once './vendor/autoload.php';

namespace App\Controllers;

include_once __DIR__ . "../../../model/database.php";

class UserControllers
{
     public static function getAll($pdo)
     {
          $sql = "SELECT * FROM user";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
          echo json_encode([
               'message' => "Successfully Get All user",
               'data' => [
                    'user' => $result
               ]
          ]);
     }
}
