<?php
// ©️Bryan Rodriguez Abad - 100523553

// 1. ESTABLECER ZONA HORARIA
date_default_timezone_set('America/Santo_Domingo');

require_once 'config.php';

// 2. AJUSTAR ZONA HORARIA EN MYSQL
if (isset($mysqli)) {
    $mysqli->query("SET time_zone = '-04:00'");
}

/* ----------------------------------------------------
   VERIFICAR SESIÓN Y ROLES
---------------------------------------------------- */
function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit();
    }
}

function is_admin() {
    return (isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === 'admin');
}

function is_client() {
    return (isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === 'cliente');
}

function is_operator() {
    return (isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === 'operador');
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        header('Location: menu.php?error=no_autorizado');
        exit();
    }
}

function require_client() {
    require_login();
    if (!is_client()) {
        header('Location: menu.php?error=solo_clientes');
        exit();
    }
}

/* -----------------------------------/* ----------------------------------------------------
   INICIAR SESIÓN (Versión Texto Plano - Directa)
---------------------------------------------------- */
function login_user($mysqli, $identificador, $password) {
    // Buscamos por username O por correo
    $stmt = $mysqli->prepare("
        SELECT id, username, correo, password_hash, nombre, rol
        FROM usuarios
        WHERE (username = ? OR correo = ?)
        LIMIT 1
    ");

    $stmt->bind_param('ss', $identificador, $identificador);
    $stmt->execute();

    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if ($row) {
        // COMPARACIÓN DIRECTA (Sin hash para asegurar que entres)
        if ($password === $row['password_hash']) {
            $_SESSION['user'] = [
                'id' => $row['id'],
                'username' => $row['username'],
                'correo' => $row['correo'],
                'nombre' => $row['nombre'],
                'rol' => $row['rol'] 
            ];
            return true;
        }
    }
    return false;
}

/* ----------------------------------------------------
 /* ----------------------------------------------------
   REGISTRAR USUARIO (Versión compatible con tu Login actual)
---------------------------------------------------- */
function register_user($mysqli, $username, $correo, $nombre, $password, $rol = 'cliente')
{
    // Eliminamos el password_hash para guardar el texto directo
    $stmt = $mysqli->prepare("
        INSERT INTO usuarios (username, correo, nombre, password_hash, rol)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    // Guardamos $password directamente en lugar del hash
    $stmt->bind_param('sssss', $username, $correo, $nombre, $password, $rol);

    return $stmt->execute();
}

/* ----------------------------------------------------
   CERRAR SESIÓN
---------------------------------------------------- */
function logout() {
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