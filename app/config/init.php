<?php

session_start();

define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');

define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/theplayground');

define('DB_HOST', 'aws-0-ap-northeast-1.pooler.supabase.com');
define('DB_PORT', '5432');
define('DB_NAME', 'postgres');
define('DB_USER', 'postgres.cmaxgrnbwpoamdrjrjve');
define('DB_PASS', 'angpogimo10');
define('DB_CHARSET', 'utf8mb4');

date_default_timezone_set('Asia/Manila');