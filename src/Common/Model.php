<?php

namespace App\Common;

class Model {

  function _load($raw) {
    foreach ($raw as $key => $value) {
      $this->$key = $value;
    }
  }

  function __set($name, $value) {
    if (!isset($this->$name))
      return;

    $this->$name = $value;
  }
}
