<?php

use Database\DatabaseConnection;
use View\ViewEngine;

const SRC_DIR = __DIR__ . '/src/';
const TEMPLATES_FIR = __DIR__ . '/templates/';

// Setup autoloading - cannot use spl_autoload as it doesn't work on the live server
function myAutoload(string $className) {
    /** @noinspection PhpIncludeInspection */
    require SRC_DIR . strtr($className, '\\', '/') . '.php';
}
spl_autoload_register('myAutoload');

// Load functions file
require_once __DIR__ . '/src/functions.php';

// Load the config but in isolated scope
$configInit = function () {
    require_once __DIR__ . '/config.php';
};
$configInit();

DatabaseConnection::init();
Router::init();
ViewEngine::init();

// Load the routing but in isolated scope
$routesInit = function () {
    require_once __DIR__ . '/actions.php';
};
$routesInit();

session_start();
Auth::init();