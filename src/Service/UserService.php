<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Provider\Database\Database;
use App\Provider\Database\IDatabase;

class UserService {
  private IDatabase $database;
  private UserRepository $userRepository;

  function __construct() {
    $this->database = Database::newConnection();
    $this->userRepository = new UserRepository($this->database);
  }

  function query() {
  }

  function getOne($args) {
  }

  function create($args) {
  }

  function delete($args) {
  }
}
