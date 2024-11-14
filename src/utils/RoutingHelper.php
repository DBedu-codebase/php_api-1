<?php
require_once __DIR__ . '/../routes/index.php';

function routeMethod($path, $method, $callback)
{
     route($path, function () use ($method, $callback) {
          if ($_SERVER['REQUEST_METHOD'] === $method) {
               echo $callback();
               return;
          }

          // Set HTTP response code to 405 and exit if method does not match
          http_response_code(405);
          echo json_encode(['error' => 'Method Not Allowed']);
          exit;
     });
}
function getRoute($path, $callback)
{
     routeMethod($path, 'GET', $callback);
};
function postRoute($path, $callback)
{
     routeMethod($path, 'POST', $callback);
};
function putRoute($path, $callback)
{
     routeMethod($path, 'PUT', $callback);
};
function deleteRoute($path, $callback)
{
     routeMethod($path, 'DELETE', $callback);
};
