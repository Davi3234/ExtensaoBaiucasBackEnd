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

  function status($status) {
    http_response_code($status);
    return $this;
  }

  function send(Result $data) {
    $data = (object)(array)$data->getResult();

    echo json_encode($data);
  }
}