<?php

namespace App\Core\Components;

abstract class Middleware {

  abstract function perform(Request $request, Response $response);
}