<?php
// ©️Bryan Rodriguez Abad - 100523553
require_once 'functions.php';

// Si ya está logueado, mandarlo al menú
if(is_logged_in()) { 
    header('Location:menu.php'); 
    exit; 
}

$err = '';

// Procesar el formulario cuando se hace POST
if($_POST) {
    $u = trim($_POST['usuario']);
    $p = $_POST['clave'];
    
    if(login_user($mysqli, $u, $p)) {
        header('Location:menu.php'); 
        exit;
    } else { 
        $err = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!doctype html>
<html class="h-full bg-gray-50" lang="es">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ParkControl - Login</title>
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
              <h2 class="text-2xl font-bold text-gray-900">Iniciar Sesión</h2>
              
              <?php if($err): ?>
                <div class="mt-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm">
                    <?php echo $err; ?>
                </div>
              <?php endif; ?>
            </div>

            <form action="index.php" method="POST" class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700" for="usuario">Usuario</label>
                <div class="mt-1 relative">
                  <input class="block w-full pl-4 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all text-sm" 
                         id="usuario" name="usuario" placeholder="Nombre de usuario" required type="text" />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700" for="clave">Contraseña</label>
                <div class="mt-1 relative">
                  <input class="block w-full pl-4 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all text-sm" 
                         id="clave" name="clave" placeholder="••••••••" required type="password" />
                </div>
              </div>

              <div>
                <button class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none transition-all uppercase tracking-wider" type="submit">
                  Ingresar al Sistema
                </button>
              </div>
            </form>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>