<?php

namespace App\Service;

use App\Repository\IUserRepository;

class UserService {

  function __construct(
    private IUserRepository $userRepository
  ) {
  }

  function query() {
    $users = $this->userRepository->findMany();

    $raw = [];
    foreach ($users as $user) {
      $raw[] = [
        'id' => $user->getId(),
        'name' => $user->getName(),
        'login' => $user->getLogin(),
      ];
    }

    return $raw;
  }

  function getOne($args) {
  }

  function create($args) {
  }

  function delete($args) {
  }
}
