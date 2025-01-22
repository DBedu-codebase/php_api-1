<?php
require_once './vendor/autoload.php';
require_once __DIR__ . '/../controllers/blog/BlogControllers.php';
require_once __DIR__ . '/../utils/RoutingHelper.php';

use App\Http\Router;
use App\Controllers\BlogControllers;
use App\Model\Database;

// * define input json
$input = json_decode(file_get_contents('php://input'), true);
header("Content-Type: application/json");

$PDO = new Database();
$router = new Router();
$router->get('/api/v1/blog', fn() => BlogControllers::getAll($PDO->getPdo()));
// Route to get a blog post by ID
$router->get('/api/v1/blog/:id', fn($id) => BlogControllers::getBlogById($PDO->getPdo(), $id));
$router->delete('/api/v1/blog/:id', fn($id) => BlogControllers::delete($PDO->getPdo(), $id));
$router->run();
