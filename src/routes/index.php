<?php
require_once './vendor/autoload.php';
require_once __DIR__ . '/../controllers/blog/BlogControllers.php';
require_once __DIR__ . '/../controllers/User/UserController.php'; // Corrected path for UserControllers
require_once __DIR__ . '/../utils/RoutingHelper.php';

use App\Http\Router;
use App\Controllers\BlogControllers;
use App\Controllers\UserControllers; // No change needed here
use App\Config\Config;

$input = json_decode(file_get_contents('php://input'), true);
header("Content-Type: application/json");

$PDO = new Config();
$router = new Router();
$router->post('/api/v1/auth/register', fn() => UserControllers::register($PDO->getPdo()));
$router->post('/api/v1/auth/login', fn() => UserControllers::login($PDO->getPdo()));
$router->get('/api/v1/blog', fn() => BlogControllers::getAll($PDO->getPdo()));
$router->post('/api/v1/blog', fn() => BlogControllers::getAll($PDO->getPdo()));
$router->put('/api/v1/blog/:id', fn($id) => BlogControllers::getAll($PDO->getPdo()));
$router->get('/api/v1/blog/:id', fn($id) => BlogControllers::getBlogById($PDO->getPdo(), $id));
$router->delete('/api/v1/blog/:id', fn($id) => BlogControllers::delete($PDO->getPdo(), $id));
$router->run();
