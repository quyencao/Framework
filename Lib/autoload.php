<?php

spl_autoload_register(function($class) {
    $parts = explode('/', str_replace("\\", '/', $class) . '.php');

    $file = '';
    foreach ($parts as $part) {
        $file .= ucfirst($part) . '/';
    }
    $file = BASE_DIR . '/' . rtrim($file, '/');

    if (file_exists($file)) {
        require_once $file;
    }
});
