<?php
require_once 'config.php';

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

/* ----------------------------------------------------
INICIAR SESIÓN
---------------------------------------------------- */
function login_user($mysqli, $username, $password)
{
    $stmt = $mysqli->prepare("
        SELECT id, username, password_hash, nombre
        FROM usuarios
        WHERE username = ?
        LIMIT 1
    ");

    $stmt->bind_param('s', $username);
    $stmt->execute();

    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if ($row) {
        // Soporta SHA-256 o password_hash
        $sha256 = hash('sha256', $password);

        if (
            $sha256 === $row['password_hash'] ||
            password_verify($password, $row['password_hash'])
        ) {
            // Guardar sesión segura
            $_SESSION['user'] = [
                'id' => $row['id'],
                'username' => $row['username'],
                'nombre' => $row['nombre'],
            ];
            return true;
        }
    }

    return false;
}

/* ----------------------------------------------------
REGISTRAR USUARIO
---------------------------------------------------- */
function register_user($mysqli, $username, $nombre, $password, $rol = 'admin')
{
    // Generar hash seguro
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
?>
