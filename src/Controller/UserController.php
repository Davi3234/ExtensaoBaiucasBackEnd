<?php

namespace App\Controller;

use App\Core\Components\Request;
use App\Core\Components\Response;

class UserController {

  function update(Request $request, Response $response) {
    return ['id' => $request->getParam('id')];
  }
}
