<?php

require_once 'functions.php';
require_login();

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú Principal - Park Control</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app">
        <div class="card">
            <div class="card-header">
                <h2>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?></h2>
                <p>Panel de Administración de Parqueo</p>
            </div>
            
            <div class="card-body">
                <div style="display: grid; gap: 10px;">
                    <a href="entrada.php" class="btn primary" style="text-decoration:none; text-align:center;">
                        🚗 Registrar Entrada
                    </a>
                    <a href="salida.php" class="btn primary" style="text-decoration:none; text-align:center; background:#4a90e2;">
                        💰 Registrar Salida / Cobro
                    </a>
                    <hr>
                    <a href="index.php" class="btn" style="text-decoration:none; text-align:center; color: #ff4d4d;">
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>