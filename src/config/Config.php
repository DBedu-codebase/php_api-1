<?php

namespace App\Config;

use Dotenv\Dotenv;
use PDO;
use PDOException;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2), '.env');
$dotenv->load();
class Config
{
     private $host;
     private $user;
     private $password;
     private $dbname;
     private $pdo;

     public function __construct()
     {
          $this->host = $_SERVER['DB_HOST'];
          $this->user = $_SERVER['DB_USER'];
          $this->password = $_SERVER['DB_PASSWORD'];
          $this->dbname = $_SERVER['DB_NAME'];

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
