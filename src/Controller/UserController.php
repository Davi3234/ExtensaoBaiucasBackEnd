<?php

namespace App\Controller;

use App\Core\Components\Request;
use App\Core\Components\Response;

class UserController {

  function update(Request $request) {
    return ['id' => $request->getParam('id')];
  }
}
