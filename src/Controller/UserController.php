<?php

namespace App\Controller;

use App\Core\Components\Request;

class UserController {

  function create(Request $request) {
    var_dump($request->getParams());
    echo 'create';
  }

  function update(Request $request) {
    var_dump($request->getParam('id'));
    echo 'update';
  }
}
