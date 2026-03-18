<?php

require_once 'functions.php';
require_login();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consultamos los datos del movimiento finalizado
$res = $mysqli->query("SELECT * FROM movimientos WHERE id = $id AND estado = 'FINALIZADO'");
$data = $res->fetch_assoc();

if (!$data) {
    die("Recibo no encontrado o el vehículo aún no ha registrado salida.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago #<?php echo $id; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 20px auto; border: 1px solid #000; padding: 15px; background-color: #fff; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .total { font-size: 18px; margin: 10px 0; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px 0; }
        hr { border: 1px dashed #000; }
        .footer { font-size: 11px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="center">
        <h2 class="bold">PARK CONTROL</h2>
        <p>RECIBO DE PAGO</p>
    </div>
    <hr>
    <p><span class="bold">ID:</span> <?php echo $data['id']; ?></p>
    <p><span class="bold">PLACA:</span> <?php echo strtoupper($data['placa']); ?></p>
    <p><span class="bold">ENTRADA:</span> <?php echo $data['hora_entrada']; ?></p>
    <p><span class="bold">SALIDA:</span>  <?php echo $data['hora_salida']; ?></p>
    <hr>
    <div class="center total">
        <span class="bold">TOTAL PAGADO: RD$ <?php echo number_format($data['total_pago'], 2); ?></span>
    </div>
    <hr>
    <div class="center footer">
        <p>¡Gracias por su preferencia!</p>
        
    </div>
    <script>
        // Esto abrirá la ventana de impresión automáticamente al cargar
        window.print();
    </script>
</body>
</html>