<?php

namespace Core\HTTP;

use Core\Common\Result;

class Response {
  private $response = null;
  private static $debugResponse = [];

  function __construct() {
    header('charset=UTF-8');
  }

  static function debugInfo(string $name, $value) {
    static::$debugResponse[$name] = $value;
  }

  function status(int $status) {
    http_response_code($status);
    return $this;
  }

  function setResponse(array|object $response) {
    $this->response = $response;
  }

  function getResponse() {
    return $this->response;
  }

  function sendResponse(int $status = null) {
    if ($status)
      $this->status($status);

    $this->sendJson($this->response, $status);
  }

  function sendJson(array|object $response, int $status = null): never {
    if ($response instanceof Result) {
      $status = $response->getStatus();
      $response = $response->getResult();
    }

    if ($status)
      $this->status($status);

    if (!is_object($response))
      $response = (object)(array)$response;

    if (static::isEnableToSendDebugInfo())
      $response->debugInfo = static::$debugResponse;

    header('Content-Type: application/json');
    $response = json_encode($response);

    $this->send($response);
  }

  function send($response): never {
    exit($response);
  }

  static function isEnableToSendDebugInfo() {
    return env('ENV', 'PROD') !== 'PROD' && static::$debugResponse;
  }
}
