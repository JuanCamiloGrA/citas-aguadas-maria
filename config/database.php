<?php
date_default_timezone_set('America/Bogota');

define('DB_HOST', 'localhost');
define('DB_NAME', 'hospital_sanjose_demo');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_CHARSET', 'utf8mb4');

function get_pdo($useDatabase)
{
    $dsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;

    if ($useDatabase) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    }

    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    );

    return new PDO($dsn, DB_USER, DB_PASS, $options);
}

