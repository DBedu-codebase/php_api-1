<?php

namespace App\Controllers;

use App\Model\Database;

class BlogControllers
{
     public static function getAll($pdo)
     {
          $sql = "SELECT * FROM blog";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
          echo json_encode([
               'message' => "Successfully Get All Blog",
               'data' => [
                    'blog' => $result
               ]
          ]);
     }

     public static function getBlogById($pdo, $id)
     {
          $sql = "SELECT * FROM blog WHERE id = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([':id' => $id]);
          $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
          echo json_encode([
               'message' => "Successfully Get Blog Based Id",
               'data' => [
                    'blog' => $result
               ]
          ]);
     }
     public static function delete($pdo, $id)
     {
          $sql = "DELETE FROM blog WHERE id = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([':id' => $id]);
          echo json_encode([
               'message' => 'Deleted successfully',
               'data' => [
                    'blog' => $stmt
               ]
          ]);
     }
}
