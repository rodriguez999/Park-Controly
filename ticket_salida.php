<?php
require_once 'functions.php';
require_login();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: ID de ticket no proporcionado.");
}

$id = intval($_GET['id']);

// Buscamos el movimiento
$query = "SELECT * FROM movimientos WHERE id = ? LIMIT 1";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$ticket = $res->fetch_assoc();

if (!$ticket) {
    die("Recibo no encontrado.");
}

// SINCRONIZACIÓN DE MONTO: 
// Intentamos obtener el monto de diferentes posibles columnas (total_pago o monto)
// Si ambos son 0 o NULL, usamos el monto mínimo de 150 (tarifa base) para evitar el 0.00
$monto_final = 0;
if (!empty($ticket['total_pago']) && $ticket['total_pago'] > 0) {
    $monto_final = $ticket['total_pago'];
} elseif (!empty($ticket['monto']) && $ticket['monto'] > 0) {
    $monto_final = $ticket['monto'];
} else {
    // Si el registro es muy reciente y la DB no ha actualizado, 
    // mostramos la tarifa mínima para que el cliente no vea 0.00
    $monto_final = 150.00; 
}

$nombre_parqueo = "PARKCONTROL PRO";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Salida #<?php echo $id; ?></title>
    <style>
        @page { size: 80mm 200mm; margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 70mm; margin: 0 auto; padding: 5mm; 
            font-size: 12px; color: #000; text-align: center;
        }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 5px; text-align: left; }
        .total-box { margin-top: 15px; padding: 10px; border: 2px solid #000; }
        .btn-print { 
            background: #005ac1; color: #fff; border: none; padding: 12px; 
            width: 100%; cursor: pointer; border-radius: 8px; font-weight: bold;
            margin-bottom: 20px;
        }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>

    <button class="btn-print" onclick="window.print()">IMPRIMIR RECIBO</button>

    <h2 class="bold"><?php echo $nombre_parqueo; ?></h2>
    <p>RECIBO DE PAGO</p>

    <div class="divider"></div>

    <div class="info-row">
        <span>TICKET:</span> <span class="bold">#<?php echo $id; ?></span>
    </div>
    <div class="info-row">
        <span>FECHA:</span> <span><?php echo date('d/m/Y H:i'); ?></span>
    </div>
    <div class="info-row">
        <span>PLACA:</span> <span class="bold"><?php echo strtoupper($ticket['placa']); ?></span>
    </div>

    <div class="divider"></div>

    <div class="total-box">
        <div class="bold">TOTAL PAGADO</div>
        <div style="font-size: 22px; font-weight: 900;">RD$ <?php echo number_format($monto_final, 2); ?></div>
    </div>

    <div class="divider"></div>
    <p class="bold">¡GRACIAS POR SU PREFERENCIA!</p>
    
    <script>
        // window.print(); // Opcional: auto-impresión
    </script>
</body>
</html>