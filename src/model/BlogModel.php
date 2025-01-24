<?php

namespace App\Model;

use App\Config\Config;
use PDO;

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

     public function getById(int $id)
     {
          $PDO = $this->getPdo();
          $sql = "SELECT * FROM {$this->table} WHERE post_id = :id";
          $stmt = $PDO->prepare($sql);
          $stmt->execute([':id' => $id]);

          return $stmt->fetch(PDO::FETCH_ASSOC);
     }

     public function create(array $data): void
     {
          $PDO = $this->getPdo();
          $sql = "INSERT INTO {$this->table} (title, content) VALUES (:title, :content)";
          $stmt = $PDO->prepare($sql);
          $stmt->execute([
               ':title' => $data['title'],
               ':content' => $data['content'],
          ]);
     }

     public function update(int $id, array $data): void
     {
          $PDO = $this->getPdo();
          $sql = "UPDATE {$this->table} SET title = :title, content = :content WHERE id = :id";
          $stmt = $PDO->prepare($sql);
          $stmt->execute([
               ':id' => $id,
               ':title' => $data['title'],
               ':content' => $data['content'],
          ]);
     }

     public function delete(int $id): void
     {
          $PDO = $this->getPdo();
          $sql = "DELETE FROM {$this->table} WHERE post_id = :id";
          $stmt = $PDO->prepare($sql);
          $stmt->execute([':id' => $id]);
     }
}
