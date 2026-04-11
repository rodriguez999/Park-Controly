<?php

require_once 'functions.php';
require_login();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscamos los datos del movimiento en la base de datos
$res = $mysqli->query("SELECT * FROM movimientos WHERE id = $id");
$data = $res->fetch_assoc();

if (!$data) {
    die('Ticket no encontrado.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Entrada #<?php echo $id; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 20px auto; border: 1px solid #000; padding: 15px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        hr { border: 1px dashed #000; }
        .footer { font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="center">
        <h2 class="bold">PARK CONTROL</h2>
        <p>Ticket de Entrada</p>
    </div>
    <hr>
    <p><span class="bold">ID:</span> <?php echo $data['id']; ?></p>
    <p><span class="bold">PLACA:</span> <?php echo strtoupper(
        $data['placa'],
    ); ?></p>
    <p><span class="bold">MARCA:</span> <?php echo $data['marca']; ?></p>
    <p><span class="bold">TIPO:</span> <?php echo $data['tipo']; ?></p>
    <p><span class="bold">ENTRADA:</span> <?php echo $data[
        'hora_entrada'
    ]; ?></p>
    <p><span class="bold">TARIFA/H:</span> $<?php echo number_format(
        $data['tarifa_por_hora'],
        2,
    ); ?></p>
    <hr>
    <div class="center footer">
        <p>Conserve este ticket para su salida.</p>
        
    </div>
    <script>
       
    </script>
</body>
</html>