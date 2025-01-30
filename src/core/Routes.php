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
          $router = new App();
          $User = new User();
          $Blog = new Blog();
          // * Routes Authentication 
          $router->post('/api/v1/auth/register', fn() => $User->register());
          $router->post('/api/v1/auth/login', fn() => $User->login());
          // * Routes Blog
          $router->get('/api/v1/blog', fn() => $Blog->getAll());
          $router->get('/api/v1/blog/:id', fn($id) => $Blog->getBlogById($id));
          $router->post('/api/v1/blog', fn() => $Blog->create());
          $router->put('/api/v1/blog/:id', fn($id) => $Blog->update($id));
          $router->delete('/api/v1/blog/:id', fn($id) => $Blog->delete($id));
          $router->run();
     }
}
