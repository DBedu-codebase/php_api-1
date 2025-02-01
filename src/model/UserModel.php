<?php

namespace App\Model;

use App\Config\Config;
use PDO;

class UserModel extends Config
{
     private $table = 'user';

     public function getUniqueUser(string $email, string $name): array {}
     public function getUniqueUserEmail(string $email): array {}
     public function PostUniqueUser(string $email, string $name, string $password) {}
}
