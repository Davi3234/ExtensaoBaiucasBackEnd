<?php

namespace App\Core\Components;

class Response {
  private $dataResponse = null;

  function __construct() {
    header('charset=UTF-8');
  }

  function status(int $status) {
    http_response_code($status);
    return $this;
  }

  function setDataResponse($dataResponse) {
    $this->dataResponse = $dataResponse;
  }

  function getDataResponse() {
    return $this->dataResponse;
  }

  function sendDataResponse(int $status = null) {
    if ($status)
      $this->status($status);

    $this->sendJson($this->dataResponse, $status);
  }

  function sendJson(array|object $data, int $status = null) {
    if ($data instanceof Result) {
      $status = $data->getStatus();
      $data = $data->getResult();
    }

    if ($status)
      $this->status($status);

    if (is_object($data))
      $data = (object)(array)$data;

    header('Content-Type: application/json');
    $data = json_encode($data);

    exit($data);
  }
}
