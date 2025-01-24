<?php

namespace App\Config;

use PDO;
use PDOException;

class Config
{
     private $host;
     private $user;
     private $password;
     private $dbname;
     private $pdo;

     public function __construct()
     {
          $dotenv = \DotenvVault\DotenvVault::createImmutable(__DIR__);
          $dotenv->safeLoad();

          $this->host = "localhost:3306";
          $this->user = "root";
          $this->password = "satelkermel123";
          $this->dbname = "blog_api";

          try {
               $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
               $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          } catch (PDOException $e) {
               die("Connection failed: " . $e->getMessage());
          }
     }

     public function getPdo()
     {
          return $this->pdo;
     }
}
