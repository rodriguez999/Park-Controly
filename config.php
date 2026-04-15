<?php
session_start();

// Datos de conexión
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_parkeo');

// Conexión a la base de datos
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) 
{
    die('DB connection error: ' . $mysqli->connect_error);
}

// Obtener configuración global
function get_config($mysqli) 
{
    $res = $mysqli->query("
        SELECT tarifa_hora, capacidad_total
        FROM configuracion
        WHERE id = 1
        LIMIT 1
");

if ($res && $row = $res->fetch_assoc()) 
{
    return $row;
}

// Valores por defecto en caso de error
return 
    [
        'tarifa_hora' => 150.00,
        'capacidad_total' => 100
    ];
}