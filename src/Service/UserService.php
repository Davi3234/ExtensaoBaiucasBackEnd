<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Provider\Database\Database;
use App\Provider\Database\IDatabase;

class UserService {
  private IDatabase $database;
  private UserRepository $userRepository;

  function __construct() {
    $this->database = Database::getGlobalConnection();
    $this->userRepository = new UserRepository($this->database);
  }

  function query() {
    return $this->userRepository->findMany();
  }

  function getOne($args) {
  }

  function create($args) {
  }

  function delete($args) {
  }
}
