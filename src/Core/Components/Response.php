<?php

namespace App\Core\Components;

class Response {

  private static $instance = null;

  static function getInstance() {
      if (!isset(self::$instance))
          self::$instance = new self();

      return self::$instance;
  }

  private function __construct() { }

}