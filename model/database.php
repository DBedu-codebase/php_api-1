<?php
$host = "localhost:3306";
$user = "root";
$password = "satelkermel123";
$dbname = "blog_api";
try {
     $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
     die("Connection failed: " . $e->getMessage());
}
