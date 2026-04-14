<?php
// 1. ESTABLECER ZONA HORARIA (Esto corrige el desfase de las 6 horas)
date_default_timezone_set('America/Santo_Domingo');

require_once 'config.php';

// 2. AJUSTAR ZONA HORARIA EN MYSQL
// Esto asegura que CURRENT_TIMESTAMP en la base de datos coincida con PHP
if (isset($mysqli)) {
    $mysqli->query("SET time_zone = '-04:00'");
}

/* ----------------------------------------------------
VERIFICAR SESIÓN
---------------------------------------------------- */
function is_logged_in()
{
    return isset($_SESSION['user']);
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location:index.php');
        exit();
    }
}

/**
 * Verifica si el usuario logueado tiene el rol de administrador.
 */
function is_admin()
{
    return (isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === 'admin');
}

/**
 * Restringe el acceso solo a administradores.
 */
function require_admin()
{
    require_login();
    if (!is_admin()) {
        header('Location:menu.php?error=no_autorizado');
        exit();
    }
}

/* ----------------------------------------------------
INICIAR SESIÓN
---------------------------------------------------- */
function login_user($mysqli, $username, $password)
{
    $stmt = $mysqli->prepare("
        SELECT id, username, password_hash, nombre, rol
        FROM usuarios
        WHERE username = ?
        LIMIT 1
    ");

    $stmt->bind_param('s', $username);
    $stmt->execute();

    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if ($row) {
        $sha256 = hash('sha256', $password);

        if (
            $sha256 === $row['password_hash'] ||
            password_verify($password, $row['password_hash'])
        ) {
            $_SESSION['user'] = [
                'id' => $row['id'],
                'username' => $row['username'],
                'nombre' => $row['nombre'],
                'rol' => $row['rol'] 
            ];
            return true;
        }
    }

    return false;
}

/* ----------------------------------------------------
REGISTRAR USUARIO
---------------------------------------------------- */
function register_user($mysqli, $username, $nombre, $password, $rol = 'usuario')
{
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("
        INSERT INTO usuarios (username, nombre, password_hash, rol)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param('ssss', $username, $nombre, $hash, $rol);

    return $stmt->execute();
}

/* ----------------------------------------------------
CERRAR SESIÓN
---------------------------------------------------- */
function logout()
{
    session_unset();
    session_destroy();
}

/* ----------------------------------------------------
CONFIGURACIÓN VISUAL GLOBAL (Tailwind)
---------------------------------------------------- */
function get_tailwind_config() {
    return "
    <script src='https://cdn.tailwindcss.com'></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#005ac1',
                    }
                }
            }
        }
    </script>";
}
?>