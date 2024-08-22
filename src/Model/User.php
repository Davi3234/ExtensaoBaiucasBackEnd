<?php

namespace App\Model;

class User {
    private $id;
    private $login;
    private $senha;

    function get($propName) {
        return $this->$propName;
    }

    function set($propName, $propValue) {
        $this->$propName = $propValue;
    }
}
