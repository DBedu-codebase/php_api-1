<?php

namespace App\Core;

class Validation
{
     private $rules = [];

     public function addRule($key, $rule)
     {
          $this->rules[$key] = $rule;
     }

     public function validate($input)
     {
          $errors = [];

          foreach ($this->rules as $key => $rule) {
               if (!isset($input[$key]) || empty($input[$key])) {
                    $errors[$key] = $rule . ' is required';
               } else {
                    list($rule, $params) = $this->getRuleParams($rule);

                    switch ($rule) {
                         case 'email':
                              if (!filter_var($input[$key], FILTER_VALIDATE_EMAIL)) {
                                   $errors[$key] = 'Invalid ' . $rule;
                              }
                              break;
                         case 'string':
                              if (!is_string($input[$key])) {
                                   $errors[$key] = 'Invalid ' . $rule;
                              }
                              break;
                         case 'min':
                              if (strlen($input[$key]) < $params) {
                                   $errors[$key] = $rule . ' of ' . $params . ' characters is required';
                              }
                              break;
                         case 'max':
                              if (strlen($input[$key]) > $params) {
                                   $errors[$key] = $rule . ' of ' . $params . ' characters is required';
                              }
                              break;
                         case 'password':
                              if (!preg_match('/^(?=.*[a-zA-Z])(?=.*[0-9]).{8,}$/', $input[$key])) {
                                   $errors[$key] = 'Password must contain at least one letter, one number and be at least ' . $params . ' characters long';
                              }
                              break;
                    }
               }
          }

          return $errors;
     }

     private function getRuleParams($rule)
     {
          $params = explode(':', $rule);

          return [array_shift($params), (int) implode(':', $params)];
     }
}
