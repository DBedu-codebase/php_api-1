<?php

namespace App\Controllers;

class blogTestingControllers
{
     public function getBlog($test)
     {
          echo json_encode(['test' => $test], JSON_PRETTY_PRINT);
     }
};
