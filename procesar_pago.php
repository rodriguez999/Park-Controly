<?php
// Desactivar la visualización de errores de texto para que no rompan el JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'functions.php';

// Capturar el cuerpo de la solicitud (JSON enviado por el Dashboard)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Respuesta por defecto
$response = ['status' => 'error', 'message' => 'Error desconocido'];

if (isset($data['id'], $data['metodo'])) {
    $id = intval($data['id']);
    $metodo = $mysqli->real_escape_string($data['metodo']);
    $monto = floatval($data['monto'] ?? 0);
    $fecha_salida = date('Y-m-d H:i:s');

    // Actualizamos el movimiento a estado SALIDA
    $query = "UPDATE movimientos SET 
                estado = 'SALIDA', 
                hora_salida = '$fecha_salida', 
                monto_pagado = $monto, 
                metodo_pago = '$metodo' 
              WHERE id = $id";

    if ($mysqli->query($query)) {
        $response = [
            'status' => 'success',
            'message' => 'Pago procesado correctamente',
            'id' => $id
        ];
    } else {
        $response['message'] = "Error en la base de datos: " . $mysqli->error;
    }
} else {
    $response['message'] = "Datos incompletos en la solicitud.";
}

// Enviamos la respuesta final como JSON
echo json_encode($response);
exit;