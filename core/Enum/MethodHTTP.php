<?php

namespace Core\Enum;

enum MethodHTTP: string {
  case GET = 'GET';
  case POST = 'POST';
  case PUT = 'PUT';
  case PATCH = 'PATCH';
  case DELETE = 'DELETE';
  case HEAD = 'HEAD';
  case OPTIONS = 'OPTIONS';
}
