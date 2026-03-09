<?php

require_once 'functions.php';

if(is_logged_in())
    { 
        header('Location:menu.php'); exit; 
    }

    $err = '';

if($_POST)
    {
        $u = trim($_POST['usuario']);
        $p = $_POST['clave'];
        if(login_user($mysqli,$u,$p))
            {
                header('Location:menu.php'); exit;
            } 

else 
    { 
        $err = 'Usuario o contraseña incorrectos.';
    }
}
?>

<!doctype html>
<html>

<head>
<meta charset='utf-8'>
<title>Park Control - Login</title>
<link rel="stylesheet" href="style.css">
</head>

<body>
<div class='app'>
    <div class='card'>
        <div class='card-header'>
        <h2>Sistema de Administracion de Parqueo</h2>
        </div>
        <div class='card-body'>
            <div class='logo-wrap'>
            <img src='Logo.png' alt='logo'>
        </div>

<?php if($err): ?><div class='alert'><?php echo $err; ?></div><?php
endif; ?>
    <form method='post'>
        <input class='input' name='usuario' placeholder='Usuario' required>
        <input class='input' type='password' name='clave'
placeholder='Contraseña' required>
                <button class='btn primary'>Ingresar</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>