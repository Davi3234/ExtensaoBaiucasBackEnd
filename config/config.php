<?php

declare(strict_types=1);
date_default_timezone_set(env('TIMEZONE', 'UTC'));
error_reporting(E_ALL & ~(E_NOTICE | E_WARNING));
@ini_set('default_charset', 'UTF-8');
@ini_set('internal_encoding', 'UTF-8');
@ini_set('input_encoding', 'UTF-8');
@ini_set('output_encoding', 'UTF-8');
@ini_set('default_mimetype', 'application/json');