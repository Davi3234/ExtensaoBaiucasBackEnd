<?php

namespace App\Core\Components;

interface ResultModel {
    function getResponse();
    function isSuccess();
    function getValue();
    function getError();
    function getStatus();
    function getResult();
}

class Result implements ResultModel {
    private $ok = true;
    private $status = 200;
    private $value = null;
    private $error = null;

    private function __construct($ok, $status, $value = null, mixed $error = null) {
        $this->ok = $ok;
        $this->status = $status;
        $this->value = $value;
        $this->error = $error;
    }

    static function success($value, $status = 200) {
        return new Result(true, $status, $value, null);
    }

    static function failure(mixed $error, $status = 400) {
        return new Result(false, $status, null, $error);
    }

    static function inherit($ok = true, $status = 200, $value = null, mixed $error = null) {
        return new Result($ok, $status, $value, $error);
    }

    function getResponse() {
        if ($this->isSuccess())
            return $this->getValue();

        return $this->getError();
    }

    function isSuccess() {
        return $this->ok;
    }

    function getValue() {
        return $this->value;
    }

    function getError() {
        return $this->error;
    }

    function getStatus() {
        return $this->status;
    }

    function getResult() {
        return ['ok' => $this->ok, 'status' => $this->status, 'value' => $this->value, 'error' => $this->error];
    }
}