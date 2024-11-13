<?php
header("Content-Type: application/json");
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        handleGet($pdo);
        break;
    case 'POST':
        handlePost($pdo, $input);
        break;
    case 'PUT':
        handlePut($pdo, $input);
        break;
    case 'DELETE':
        handleDelete($pdo, $input);
        break;
    default:
        echo json_encode(['message' => 'Invalid request method']);
        break;
}

function handleGet($pdo) {
    $sql = "SELECT * FROM blog_posts";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
}

function handlePost($pdo, $input) {
    $sql = "INSERT INTO blog_posts (title, content, category, tags) VALUES (:title, :content, :category, :tags)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['title' => $input['title'], 'content' => $input['content'], 'category' => $input['category'], 'tags' => $input['tags']]);
    echo json_encode(['message' => 'User created successfully']);
}

function handlePut($pdo, $input) {
    $sql = "UPDATE blog_posts SET title = :title, content = :content, category = :category, tags = :tags WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['title' => $input['title'], 'content' => $input['content'], 'category' => $input['category'], 'tags' => $input['tags'], 'id' => $input['id']]);
    echo json_encode(['message' => 'User updated successfully']);
}

function handleDelete($pdo, $input) {
    $sql = "DELETE FROM blog_posts WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $input['id']]);
    echo json_encode(['message' => 'User deleted successfully']);
}
?>