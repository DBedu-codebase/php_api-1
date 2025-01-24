<?php

namespace App\Core;

use App\Core\App;
use App\Controllers\Blog\Blog;
use App\Controllers\User\User;
use App\Config\Config;



class Routes extends Config
{
     public function run()
     {
          // $PDO = new Config();
          $router = new App();
          $User = new User();
          $Blog = new Blog();
          $router->post('/api/v1/auth/register', fn() => $User->register($this->getPdo()));
          $router->post('/api/v1/auth/login', fn() => $User->login($this->getPdo()));
          $router->get('/api/v1/blog', fn() => $Blog->getAll($this->getPdo()));
          $router->post('/api/v1/blog', fn() => $Blog->getAll($this->getPdo()));
          $router->put('/api/v1/blog/:id', fn($id) => $Blog->getAll($this->getPdo()));
          $router->get('/api/v1/blog/:id', fn($id) => $Blog->getBlogById($this->getPdo(), $id));
          $router->delete('/api/v1/blog/:id', fn($id) => $Blog->delete($this->getPdo(), $id));
          $router->run();
     }
}
