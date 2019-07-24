<?php
require_once __DIR__ . '/../init.php';

Router::getInstance()->dispatchRequest()->send();