<?php

namespace App\Core\Components;

class Response {

  private static $instance = null;

  static function getInstance() {
    if (!isset(self::$instance))
      self::$instance = new self();

    return self::$instance;
  }

  private function __construct() {
    header('charset=UTF-8');
  }

  function status($status) {
    http_response_code($status);
    return $this;
  }

  function sendJson($data, $status = null) {
    if ($status)
      $this->status($status);

    if ($data instanceof Result)
      $data = $data->getResult();

    if (is_object($data)) {
      $data = (object)(array)$data;
    }

    header('Content-Type: application/json');
    $data = json_encode($data);

    exit($data);
  }
}
