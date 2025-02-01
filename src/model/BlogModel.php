<?php

namespace App\Model;

use App\Config\Config;
use PDO;
use stdClass;

class BlogModel extends Config
{
     private $table = 'blog';

     public function getAll(): array
     {
          $PDO = $this->getPdo();
          $sql = "SELECT * FROM {$this->table}";
          $stmt = $PDO->prepare($sql);
          $stmt->execute();

          return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }

     public function getById(int $id) {}

     public function create(array $data, stdClass  $payload): array {}

     public function update(int $id, array $data, stdClass $payload): array {}

     public function confirm_author(int $id, stdClass $payload): void {}

     public function delete(int $id): void {}
}
