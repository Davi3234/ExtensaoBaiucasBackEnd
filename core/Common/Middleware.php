<?php

namespace Core\Common;

use Core\HTTP\Request;
use Core\HTTP\Response;

abstract class Middleware {

  abstract function perform(Request $request, Response $response): void;
}
