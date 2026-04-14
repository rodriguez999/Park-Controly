<?php
require_once 'functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = $_POST['nombre'] ?? '';
    $nuevo_username = $_POST['username'] ?? '';
    // Aquí podrías guardar también el color e icono seleccionado en la base de datos
    
    // Simulación de guardado en base de datos
    $_SESSION['user']['nombre'] = $nuevo_nombre;
    $_SESSION['user']['username'] = $nuevo_username;
    
    // Redirigir con éxito
    header("Location: perfil.php?success=1");
    exit;
}