<?php

require_once 'functions.php'; 
require_login();

$cfg = get_config($mysqli);
$msg = '';

if($_POST)
{
    $placa = strtoupper(trim($_POST['placa']));
    $marca = trim($_POST['marca']);
    $tipo = $_POST['tipo'];
    $memb = $_POST['membresia'];
    $tarifa = floatval($cfg['tarifa_hora']);
    $estado = 'EN_PARQUEO';

// Contar vehículos actualmente en parqueo
    $cnt = $mysqli->query("SELECT COUNT(*) AS c FROM movimientos WHERE
estado='EN_PARQUEO'")
                ->fetch_assoc()['c'];

if($cnt >= $cfg['capacidad_total'])
    {
        $msg = 'No hay cupos disponibles.';
    } 
else 
    {
        $h = date('Y-m-d H:i:s');

    // INSERT
    $st = $mysqli->prepare("
        INSERT INTO movimientos 
        (placa, marca, tipo, hora_entrada, tarifa_por_hora, estado, 
membresia, tipo_tarifa) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $st->bind_param(
        'ssssdsss',
        $placa,
        $marca,
        $tipo,
        $h,
        $tarifa,
        $estado,
        $memb,
        $tipo
    );

    if($st->execute()){
        $id = $mysqli->insert_id;
        $msg = "Entrada registrada. 
                <a style='color:#64ffd3;font-weight:bold;' 
                href='ticket.php?id={$id}' 
                target='_blank'>
                Ver ticket
            </a>";
    }
    else
        {
            $msg = 'Error al registrar: ' . $st->error;
        }
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<title>Registrar entrada</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class='app'>
    <div class='card'>

        <div class='card-header'>
            <h2>Registrar entrada</h2>
        </div>
        
        <div class='card-body'>
            
        <!-- FORMULARIO -->
        <form method='post' autocomplete="off">

            <input class='input' name='placa' placeholder='Placa del 
vehículo' required>

            <input class='input' name='marca' placeholder='Marca'>
            
            <!-- TIPO DE VEHÍCULO -->
            <select class='input' name='tipo' required>
                <option value="" disabled selected>Seleccionar tipo de 
vehículo</option>
                <option value="Automóvil">Automóvil</option>
                <option value="Jeepeta">Jeepeta</option>
                <option value="Camioneta">Camioneta</option>
            </select>
            
            <!-- MEMBRESÍA -->
            <select class='input' name='membresia' required>
            <option value="" disabled selected>Seleccionar tipo de 
membresía</option>

            <option value='NINGUNA'>Sin membresía</option>
            <option value='PLATA'>PLATA</option>
            <option value='ORO'>ORO</option>
            <option value='PREMIUM'>PREMIUM</option>
        </select>
        
        <!-- MENSAJE -->
        <div class='msg'>
            <?php echo $msg; ?>
        </div>
    
            <!-- BOTONES -->
            <button class='btn primary'>Registrar</button>
            <a class='btn' href='menu.php'>Regresar</a>
            
            </form>
        
        </div>
    </div>
</div>

</body>
</html>         