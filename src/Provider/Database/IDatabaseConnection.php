<?php

namespace App\Provider\Database;

interface IDatabaseConnection {
  function connect();
  function close();
  function getError(): string;
}
