<?php
require_once 'functions.php';

$err = '';
$msg = '';

if($_POST) {
    $username  = trim($_POST['username']);
    $nombre    = trim($_POST['nombre']);
    $password  = $_POST['password'];
    $password2 = $_POST['password2'];

    if($password !== $password2) {
        $err = 'Las contraseñas no coinciden.';
    } else {
        // Usamos la función de functions.php
        if(register_user($mysqli, $username, $nombre, $password)) {
            $msg = 'Usuario registrado correctamente. Ahora puedes iniciar sesión.';
        } else {
            $err = 'Error al registrar usuario. Puede que el nombre de usuario ya exista.';
        }
    }
}
?>
<!doctype html>
<html class="h-full bg-gray-50" lang="es">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ParkControl - Registrar</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            animation: {
              "slide-in-left": "slideInLeft 0.6s ease-out forwards",
              "slide-in-right": "slideInRight 0.6s ease-out forwards",
            },
            keyframes: {
              slideInLeft: {
                "0%": { transform: "translateX(-100%)", opacity: "0" },
                "100%": { transform: "translateX(0)", opacity: "1" },
              },
            },
          },
        },
      };
    </script>
    <style>
      .bg-hero-image {
        background-image: linear-gradient(rgba(30, 58, 138, 0.8), rgba(30, 58, 138, 0.8)),
                          url("https://images.unsplash.com/photo-1506521781263-d8422e82f27a?q=80&w=2070&auto=format&fit=crop");
        background-size: cover;
        background-position: center;
      }
      .min-h-screen-custom { min-height: 100vh; }
    </style>
  </head>
  <body class="h-full font-sans antialiased">
    <main class="flex min-h-screen-custom flex-col md:flex-row">
      <section class="hidden md:flex md:w-1/2 bg-hero-image text-white p-12 flex-col justify-center relative overflow-hidden">
        <div class="max-w-md mx-auto space-y-8 relative z-10">
          <div>
            <h1 class="text-5xl font-bold mb-2">ParkControl</h1>
            <h2 class="text-3xl font-semibold mb-4">Sistema de Gestión de Parqueos</h2>
            <p class="text-blue-100 text-lg">Administra tus estacionamientos de forma fácil y eficiente.</p>
          </div>
          <div class="space-y-6 pt-8">
            <div class="flex items-start gap-4">
              <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
              </div>
              <div>
                <h3 class="font-bold text-lg">Control Total</h3>
                <p class="text-blue-100 text-sm">Entradas y salidas registradas en tiempo real.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="flex-1 flex items-center justify-center p-6 bg-gray-50">
        <div class="w-full max-w-md">
          <div class="bg-white p-8 md:p-10 rounded-2xl shadow-xl border border-gray-100">
            <div class="text-center mb-8">
              <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full text-white mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
              </div>
        <h2 class="text-2xl font-bold mb-6 text-center">Registrarse</h2>
                </div>
    <?php if($err): ?>
      <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm">
        <?php echo $err; ?>
      </div>
    <?php endif; ?>

    <?php if($msg): ?>
      <div class="mb-4 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm">
        <?php echo $msg; ?>
      </div>
    <?php endif; ?>

    <form action="registrar.php" method="POST" class="space-y-4">
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700">Usuario</label>
        <input type="text" name="username" id="username" required class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
      </div>
      <div>
        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre completo</label>
        <input type="text" name="nombre" id="nombre" required class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
        <input type="password" name="password" id="password" required class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
      </div>
      <div>
        <label for="password2" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
        <input type="password" name="password2" id="password2" required class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Registrarse</button>
    </form>

    <p class="mt-4 text-center text-sm">
      ¿Ya tienes cuenta? <a href="index.php" class="text-blue-600 hover:underline">Inicia sesión</a>
    </p>
  </div>
</body>
</html>