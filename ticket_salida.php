<?php
require_once 'functions.php';
require_login();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cambiamos 'FINALIZADO' por 'COMPLETADO' para que coincida con tu lógica de salida.php
// Cambia la línea 11 por esta:
$res = $mysqli->query("SELECT * FROM movimientos WHERE id = $id AND estado = 'COMPLETADO'");
$data = $res->fetch_assoc();

if (!$data) {
    die('Recibo no encontrado o el vehículo aún no ha procesado su pago.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago #<?php echo $id; ?></title>
    <style>
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 280px; 
            margin: 0 auto; 
            padding: 10px; 
            text-align: center;
        }
        .bold { font-weight: bold; }
        hr { border: none; border-top: 1px dashed #000; margin: 15px 0; }
        .info { text-align: left; font-size: 13px; }
        .total-box { 
            border-top: 2px solid #000; 
            border-bottom: 2px solid #000; 
            margin: 10px 0; 
            padding: 10px 0;
            font-size: 20px;
        }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <h2 class="bold" style="margin-bottom:0;">PARK CONTROL</h2>
    <p style="margin-top:0;">RECIBO DE PAGO</p>
    
    <hr>
    <div class="info">
        <p><b>PLACA:</b> <?php echo strtoupper($data['placa']); ?></p>
        <p><b>ENTRADA:</b> <?php echo date('d/m/y H:i', strtotime($data['hora_entrada'])); ?></p>
        <p><b>SALIDA :</b> <?php echo date('d/m/y H:i', strtotime($data['hora_salida'])); ?></p>
    </div>
    
    <div class="total-box bold">
        TOTAL: RD$ <?php echo number_format($data['total_pago'], 2); ?>
    </div>
    
    <p style="font-size: 11px;">Atendido por: <?php echo $_SESSION['user']['nombre']; ?></p>
    <hr>
    <p class="bold">¡GRACIAS POR SU PREFERENCIA!</p>

    <div class="no-print">
        <button onclick="window.print()">Imprimir</button>
        <a href="menu.php">Ir al Inicio</a>
    </div>
</body>
</html>