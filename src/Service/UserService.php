<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Provider\Database\Database;
use App\Provider\Database\IDatabase;
use App\Provider\Sql\SQL;

class UserService {
  private IDatabase $database;
  private UserRepository $userRepository;

  function __construct() {
    $this->database = Database::newConnection();
    $this->userRepository = new UserRepository($this->database);
  }

  function query() {
    return $this->userRepository->findMany(SQL::select());
  }

  function getOne($args) {
  }

  function create($args) {
  }

  function delete($args) {
  }
}
