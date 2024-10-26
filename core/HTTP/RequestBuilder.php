<?php

namespace Core\HTTP;

class RequestBuilder {

  private string $pathHttp;
  private string $methodHttp;
  private $params = [];
  private $body = [];

  function build() {
    return new Request($this->body, $this->params, [], $this->pathHttp, $this->methodHttp);
  }

  function setPathHttp(string $pathHttp) {
    $this->pathHttp = $pathHttp;

    return $this;
  }

  function setMethodHttp(string $methodHttp) {
    $this->methodHttp = $methodHttp;

    return $this;
  }

  function setParams(array $params) {
    $this->params = array_merge($this->params, $params);

    return $this;
  }

  function setBody(array $body) {
    $this->body = array_merge($this->body, $body);

    return $this;
  }
}
