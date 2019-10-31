<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * If the incoming request is an OPTIONS request
 * we will register a handler for the requested route
 */
class OptionsRequestProvider extends ServiceProvider {
    public function register() {
      $request = app('request');
      if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
          $path = explode('?', $_SERVER['REQUEST_URI']);
          app()->router->options($path[0], function() { return response('', 200); });
      }
    }
}