<?php
declare(strict_types=1);

use Database\DatabaseConfig;

$dbConfig = DatabaseConfig::init();
$dbConfig->host = 'localhost';
$dbConfig->username = 'root';
$dbConfig->password = '';
$dbConfig->database = 'cool_music';
