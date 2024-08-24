<?php

namespace App\Controller;

use App\Core\Components\Request;

class UserController {

  function update(Request $request) {
    return ['id' => $request->getParam('id')];
  }
}
