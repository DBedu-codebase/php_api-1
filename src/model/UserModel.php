<?php

namespace App\Model;

use App\Config\Config;
use PDO;

class UserModel extends Config
{
     private $table = 'user';

     public function getUniqueUser(string $email, string $name): array
     {
          $PDO = $this->getPdo();
          $sql = "SELECT email,username FROM {$this->table} WHERE email = :email OR username = :username";
          $stmt = $PDO->prepare($sql);
          $stmt->execute([
               ':email' => $email,
               ':username' => $name,
          ]);

          return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }
}
