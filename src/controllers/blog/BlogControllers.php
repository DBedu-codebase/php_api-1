<?php
$input = json_decode(file_get_contents('php://input'), true);
function Get_All_Blog($pdo)
{
     try {
          // $sql = "SELECT * FROM blog_posts";
          // $stmt = $pdo->prepare($sql);
          // $stmt->execute();
          // $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
          echo json_encode("Testing");
     } catch (PDOException $e) {
          echo json_encode(['error' => 'Database error: ' . $e->getMessage()], JSON_PRETTY_PRINT);
     }
}
function Post_Blog($pdo)
{
     global $input;
     try {
          $sql = "INSERT INTO blog_posts (title, content,category,tags) VALUES (:title,:content,:category,:tags)";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([
               ':title' => $input['title'],
               ':content' => $input['content'],
               ':category' => $input['category'],
               ':tags' => $input['tags'],
          ]);
          echo json_encode(['message' => 'Blog post created successfully']);
     } catch (PDOException $e) {
          echo json_encode(['error' => 'Failed to create blog post: ' . $e->getMessage()], JSON_PRETTY_PRINT);
     }
}
function greet()
{
     echo "hello";
}
// function Get_Single_Blog($id)
// {
//      try {
//           $sql = "SELECT * FROM blog_posts WHERE id = :id";
//           $stmt = $pdo->prepare($sql);
//           $stmt->bindParam(':id', $id);
//           $stmt->execute();
//           $result = $stmt->fetch(PDO::FETCH_ASSOC);
//           echo json_encode($result);
//      } catch (PDOException $e) {
//           echo json_encode(['error' => 'Database error: ' . $e->getMessage()], JSON_PRETTY_PRINT);
//      }
// }
