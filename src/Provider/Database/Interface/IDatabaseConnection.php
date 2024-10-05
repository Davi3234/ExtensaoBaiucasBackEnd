<?php

namespace App\Provider\Database\Interface;

interface IDatabaseConnection {
  function connect();
  function close();
  function getError(): string;
}
