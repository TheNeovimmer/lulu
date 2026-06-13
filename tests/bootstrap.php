<?php
require_once __DIR__ . '/../vendor/autoload.php';

if (!defined('DB_HOST')) {
    define('DB_HOST', 'db');
    define('DB_NAME', 'luma_test');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
    define('BASE_URL', 'https://luma.ddev.site');
}

if (!defined('TEST_MODE')) {
    define('TEST_MODE', true);
}
