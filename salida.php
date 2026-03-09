<?php

require_once 'functions.php'; require_login();
$cfg = get_config($mysqli);
$msg='';

if($_POST && !empty($_POST['placa']))
    {
        $placa = strtoupper(trim($_POST['placa']));
        $st = $mysqli->prepare("SELECT * FROM movimientos WHERE placa=? AND
estado='EN_PARQUEO' ORDER BY hora_entrada DESC LIMIT 1");
        $st->bind_param('s',$placa); $st->execute(); $mov = $st->get_result()-
>fetch_assoc();

if(!$mov) $msg='No se encontró vehículo en parqueo.';
else
    {
        $hs = date('Y-m-d H:i:s');
        $entrada = new DateTime($mov['hora_entrada']); $salida = new
        DateTime($hs);
        $diff = $salida->getTimestamp() - $entrada->getTimestamp();
        $horas = ceil($diff/3600);
        $tarifa_base = floatval($mov['tarifa_por_hora']);
        $res1 = $mysqli->prepare("SELECT factor FROM tarifas_tipo WHERE
        tipo=? LIMIT 1");

        $res1->bind_param('s',$mov['tipo']); $res1->execute(); $f1 = $res1-
        >get_result()->fetch_assoc()['factor'] ?? 1.0;

        $res2 = $mysqli->prepare("SELECT factor FROM tarifas_membresia 
        WHERE membresia=? LIMIT 1");
        $res2->bind_param('s',$mov['membresia']); $res2->execute(); $f2 =
        $res2->get_result()->fetch_assoc()['factor'] ?? 1.0;

        $tarifa_ajustada = $tarifa_base * floatval($f1) * floatval($f2);
        if($horas <= 1) $total = $tarifa_ajustada * 0.5;

        elseif($horas <= 12) $total = $horas * $tarifa_ajustada;
        elseif($horas <= 24) $total = $horas * ($tarifa_ajustada * 0.75);
        else $total = $tarifa_ajustada * 0.5;

        $st2 = $mysqli->prepare("UPDATE movimientos SET hora_salida=?, 
        total_pago=?, estado='FINALIZADO' WHERE id=?");

        $st2->bind_param('sdi',$hs,$total,$mov['id']); $st2->execute();
        $msg = "Salida registrada. Total a pagar: RD$ 
        ".number_format($total,2)." <a href='ticket_salida.php?id={$mov['id']}' 
        target='_blank'>Imprimir</a>";
    }
}
?>

<!doctype html>

<html>
<head>
<meta charset='utf-8'>
<title>Registrar salida</title>
<link rel="stylesheet" href="style.css">
</head>

<body>
<div class='app'><div class='card'><div class='card-header'><h2>Registrar 
salida</h2></div>
<div class='card-body'>
<form method='post'>
<input class='input' name='placa' placeholder='Placa del vehículo'
required>
<div class='msg'><?php echo $msg; ?></div>
<button class='btn primary'>Registrar salida</button>
<a class='btn' href='menu.php'>Regresar</a>
</form></div></div></div></body></html>