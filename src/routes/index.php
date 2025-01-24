<?php
require_once './vendor/autoload.php';

use App\Core\App;
use App\Controllers\Blog\Blog;
use App\Controllers\User\User;
use App\Config\Config;

$input = json_decode(file_get_contents('php://input'), true);
header("Content-Type: application/json");

$PDO = new Config();
$router = new App();
$User = new User();
$Blog = new Blog();
$router->post('/api/v1/auth/register', fn() => $User->register($PDO->getPdo()));
$router->post('/api/v1/auth/login', fn() => $User->login($PDO->getPdo()));
$router->get('/api/v1/blog', fn() => $Blog->getAll($PDO->getPdo()));
$router->post('/api/v1/blog', fn() => $Blog->getAll($PDO->getPdo()));
$router->put('/api/v1/blog/:id', fn($id) => $Blog->getAll($PDO->getPdo()));
$router->get('/api/v1/blog/:id', fn($id) => $Blog->getBlogById($PDO->getPdo(), $id));
$router->delete('/api/v1/blog/:id', fn($id) => $Blog->delete($PDO->getPdo(), $id));
$router->run();
