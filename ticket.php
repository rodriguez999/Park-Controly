<?php
require_once 'functions.php';
require_login();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$res = $mysqli->query("SELECT * FROM movimientos WHERE id = $id");
$data = $res->fetch_assoc();

if (!$data) {
    die('Ticket no encontrado.');
}

$config = get_config($mysqli);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Entrada #<?php echo $id; ?></title>
    <style>
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 280px; 
            margin: 0 auto; 
            padding: 10px; 
            text-align: center;
        }
        .bold { font-weight: bold; }
        .placa { font-size: 26px; margin: 10px 0; border: 2px solid #000; padding: 5px; }
        hr { border: none; border-top: 1px dashed #000; margin: 15px 0; }
        .info { text-align: left; font-size: 14px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <h2 class="bold" style="margin-bottom:0;">PARK CONTROL</h2>
    <p style="margin-top:0;">Santo Domingo, RD</p>
    
    <hr>
    <p class="bold">TICKET DE ENTRADA</p>
    <div class="placa bold"><?php echo strtoupper($data['placa']); ?></div>
    
    <div class="info">
        <p><b>FECHA:</b> <?php echo date('d/m/Y', strtotime($data['hora_entrada'])); ?></p>
        <p><b>HORA :</b> <?php echo date('H:i:s', strtotime($data['hora_entrada'])); ?></p>
        <p><b>TARIFA:</b> RD$ <?php echo number_format($config['tarifa_hora'], 2); ?> / hr</p>
    </div>
    
    <hr>
    <p style="font-size: 12px;">Por favor, conserve este ticket.<br>En caso de pérdida se aplicará un recargo.</p>
    <p class="bold">¡GRACIAS POR SU VISITA!</p>

    <div class="no-print">
        <button onclick="window.print()">Imprimir</button>
        <a href="entrada.php">Volver</a>
    </div>
</body>
</html>