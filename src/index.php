<?php

spl_autoload_register(function ($class_name) {
    include './' . str_replace('\\', '/', $class_name) . '.php';
});

use Server\App;

App::Bootstrap();

$uri = '/users/create';
