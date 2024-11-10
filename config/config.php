<?php

declare(strict_types=1);

// TIMEZONE
date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

// ERROR REPORTING
error_reporting(E_ALL & ~(E_NOTICE | E_WARNING));

// GLOBAL INI CONFIG
@ini_set('default_charset', 'UTF-8');
@ini_set('internal_encoding', 'UTF-8');
@ini_set('input_encoding', 'UTF-8');
@ini_set('output_encoding', 'UTF-8');
@ini_set('default_mimetype', 'application/json');

// HEADERS REQUEST
@header('Access-Control-Allow-Origin: *');
@header('Access-Control-Allow-Headers: Authorization, Content-Type, x-xsrf-token, x_csrftoken, Cache-Control, X-Requested-With');
@header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Constants
define('PATH_ROOT_SOURCE', __DIR__ . '/..');

define('PATH_STORAGE', PATH_ROOT_SOURCE . '/storage');
