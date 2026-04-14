<?php
require_once 'functions.php';
require_login();

$id = $_GET['id'] ?? null;
if (!$id) die("ID de ticket no proporcionado.");

// Consultar datos del movimiento y configuración
$stmt = $mysqli->prepare("SELECT * FROM movimientos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$movimiento = $stmt->get_result()->fetch_assoc();

$config = get_config($mysqli);

if (!$movimiento) die("Ticket no encontrado.");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Entrada - <?php echo $movimiento['placa']; ?></title>
    <style>
        @page { size: 80mm 200mm; margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 80mm; 
            margin: 0; 
            padding: 10px; 
            font-size: 12px;
            text-align: center;
        }
        .header { margin-bottom: 10px; }
        .logo { font-size: 18px; font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .info { text-align: left; margin-bottom: 5px; }
        .placa { font-size: 24px; font-weight: bold; margin: 10px 0; }
        .footer { font-size: 10px; margin-top: 20px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <div class="logo">PARKCONTROL</div>
        <div>Santo Domingo, RD</div>
        <div>RNC: 123-45678-9</div>
    </div>

    <div class="divider"></div>
    
    <div class="info">TICKET DE ENTRADA</div>
    <div class="info">Fecha: <?php echo date('d/m/Y', strtotime($movimiento['hora_entrada'])); ?></div>
    <div class="info">Hora: <?php echo date('H:i:s', strtotime($movimiento['hora_entrada'])); ?></div>
    
    <div class="divider"></div>
    
    <div>PLACA DEL VEHÍCULO:</div>
    <div class="placa"><?php echo $movimiento['placa']; ?></div>
    
    <div class="divider"></div>
    
    <div class="info">Tarifa por hora: RD$ <?php echo number_format($config['tarifa_hora'], 2); ?></div>
    <div class="info">Atendido por: <?php echo $_SESSION['user']['nombre']; ?></div>

    <div class="divider"></div>

    <div class="footer">
        *** NO PERDER ESTE TICKET ***<br>
        Gracias por su preferencia.<br>
        <?php echo date('Y'); ?> &copy; ParkControl
    </div>

    <button class="no-print" onclick="window.print()" style="margin-top: 20px; padding: 10px;">
        Re-imprimir Ticket
    </button>
    <br>
    <a href="entrada.php" class="no-print" style="text-decoration: none; color: blue; font-size: 14px;">Volver a Entradas</a>
</body>
</html>