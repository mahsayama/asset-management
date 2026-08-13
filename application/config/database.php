<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

// Parse environment variables first, then fallback to .env file
$env_connection = getenv('DB_CONNECTION') ?: 'sqlite';
$env_db = getenv('DB_DATABASE') ?: FCPATH . 'db.sqlite3';
$env_host = getenv('DB_HOST') ?: '127.0.0.1';
$env_port = (int)(getenv('DB_PORT') ?: 5432);
$env_user = getenv('DB_USERNAME') ?: 'postgres';
$env_pass = getenv('DB_PASSWORD') ?: '';

if (file_exists(FCPATH . '.env')) {
    $lines = file(FCPATH . '.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            if ($key === 'DB_CONNECTION' && !getenv('DB_CONNECTION')) $env_connection = $val;
            if ($key === 'DB_DATABASE' && !getenv('DB_DATABASE')) $env_db = $val;
            if ($key === 'DB_HOST' && !getenv('DB_HOST')) $env_host = $val;
            if ($key === 'DB_PORT' && !getenv('DB_PORT')) $env_port = (int)$val;
            if ($key === 'DB_USERNAME' && !getenv('DB_USERNAME')) $env_user = $val;
            if ($key === 'DB_PASSWORD' && !getenv('DB_PASSWORD')) $env_pass = $val;
        }
    }
}

// Fallback to sqlite if pgsql extension is not loaded in current PHP instance
if ($env_connection === 'pgsql' || $env_connection === 'postgre') {
    if (!function_exists('pg_connect') && !extension_loaded('pdo_pgsql')) {
        $env_connection = 'sqlite';
    }
}

if ($env_connection === 'sqlite') {
    $db_path = (strpos($env_db, '/') === false && strpos($env_db, '\\') === false) ? FCPATH . $env_db : $env_db;
    
    $db['default'] = array(
        'dsn'      => 'sqlite:' . $db_path,
        'hostname' => '',
        'username' => '',
        'password' => '',
        'database' => '',
        'dbdriver' => 'pdo',
        'subdriver' => 'sqlite',
        'dbprefix' => '',
        'pconnect' => FALSE,
        'db_debug' => (ENVIRONMENT !== 'production'),
        'cache_on' => FALSE,
        'cachedir' => '',
        'char_set' => 'utf8',
        'dbcollat' => 'utf8_general_ci',
        'swap_pre' => '',
        'encrypt'  => FALSE,
        'compress' => FALSE,
        'stricton' => FALSE,
        'failover' => array(),
        'save_queries' => TRUE
    );
} else {
    $db['default'] = array(
        'dsn'      => '',
        'hostname' => $env_host,
        'username' => $env_user,
        'password' => $env_pass,
        'database' => $env_db,
        'port'     => $env_port,
        'dbdriver' => 'postgre',
        'dbprefix' => '',
        'pconnect' => FALSE,
        'db_debug' => (ENVIRONMENT !== 'production'),
        'cache_on' => FALSE,
        'cachedir' => '',
        'char_set' => 'utf8',
        'dbcollat' => 'utf8_general_ci',
        'swap_pre' => '',
        'encrypt'  => FALSE,
        'compress' => FALSE,
        'stricton' => FALSE,
        'failover' => array(),
        'save_queries' => TRUE
    );
}
