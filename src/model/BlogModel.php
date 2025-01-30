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

     public function getById(int $id)
     {
          $PDO = $this->getPdo();
          $sql = "SELECT * FROM {$this->table} WHERE post_id = :id";
          $stmt = $PDO->prepare($sql);
          $stmt->execute([':id' => $id]);

          return $stmt->fetch(PDO::FETCH_ASSOC);
     }

     public function create(array $data, stdClass  $payload): array
     {
          $PDO = $this->getPdo();
          $sql = "INSERT INTO {$this->table} (title, content, author_id, published_at) VALUES (:title, :content, :author_id, :published_at)";
          $stmt = $PDO->prepare($sql);
          $stmt->execute([
               ':title' => $data['title'],
               ':content' => $data['content'],
               ':author_id' => $payload->id,
               ':published_at' => date('Y-m-d H:i:s')
          ]);

          return [
               'id' => $PDO->lastInsertId(),
               'title' => $data['title'],
               'content' => $data['content'],
               'author_id' => $payload->id,
               'published_at' => date('Y-m-d H:i:s')
          ];
     }

     public function update(int $id, array $data, stdClass $payload): array
     {
          $PDO = $this->getPdo();
          $sql = "UPDATE {$this->table} SET title = :title, content = :content , published_at = :published_at WHERE post_id = :id";
          $stmt = $PDO->prepare($sql);
          $stmt->execute([
               ':id' => $id,
               ':title' => $data['title'],
               ':content' => $data['content'],
               ':published_at' => date('Y-m-d H:i:s')
          ]);
          return [
               'id' => $id,
               'title' => $data['title'],
               'content' => $data['content'],
               'author_id' => $payload->id,
               'published_at' => date('Y-m-d H:i:s')
          ];
     }

     public function confirm_author(int $id, stdClass $payload): void
     {
          $PDO = $this->getPdo();
          $sql = "SELECT author_id FROM {$this->table} WHERE post_id = :id";
          $stmt = $PDO->prepare($sql);
          $stmt->execute([':id' => $id]);
          $dbAuthorId = $stmt->fetchColumn();

          if ($dbAuthorId !== $payload->id) {
               http_response_code(400);
               echo json_encode(['errors' => 'Unauthorized']);
               exit();
          }
     }

     public function delete(int $id): void
     {
          $PDO = $this->getPdo();
          $sql = "DELETE FROM {$this->table} WHERE post_id = :id";
          $stmt = $PDO->prepare($sql);
          $stmt->execute([':id' => $id]);
     }
}
