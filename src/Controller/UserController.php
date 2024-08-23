<?php

namespace App\Controller;

use App\Core\Components\Request;
use App\Core\Components\Response;
use App\Core\Components\Result;
use App\Exception\BadRequestException;

class UserController {

  function create(Request $request) {
  }

  function update(Request $request, Response $response) {
    $response->send(
      Result::success(
        [
          'id' => $request->getParam('id')
        ]
      )
    );
  }
}
