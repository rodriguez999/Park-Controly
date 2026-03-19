<?php
require_once 'functions.php';
require_login();

$user_name = $_SESSION['usuario'];

// Consulta para traer los vehículos registrados (asumiendo que tienes una tabla 'vehiculos')
// Si aún no tienes la tabla, esta consulta fallará hasta que la creemos.
$res_vehiculos = $mysqli->query("SELECT * FROM vehiculos ORDER BY id DESC");
?>
<!doctype html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ParkControl - Mis Vehículos</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              'surface-dim': '#f1f3f9',
              'primary': '#005ac1',
              'on-surface': '#1a1c1e',
              'outline-variant': '#c3c7cf',
            }
          }
        }
      }
    </script>
</head>
<body class="bg-surface-dim min-h-screen">
    <div class="flex">
        <aside class="w-20 lg:w-64 bg-white min-h-screen border-r border-outline-variant/30 flex flex-col">
            <div class="p-6 flex items-center gap-3">
                <div class="bg-primary w-10 h-10 rounded-xl flex items-center justify-center text-white">
                    <span class="material-symbols-outlined">local_parking</span>
                </div>
                <span class="font-bold text-xl hidden lg:block">ParkControl</span>
            </div>
            <nav class="flex-1 mt-4 px-3 space-y-2">
                <a href="menu.php" class="flex items-center gap-4 p-3 text-on-surface-variant hover:bg-gray-100 rounded-xl">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span class="hidden lg:block">Dashboard</span>
                </a>
                <a href="vehiculos.php" class="flex items-center gap-4 p-3 bg-primary/10 text-primary rounded-xl font-semibold">
                    <span class="material-symbols-outlined">directions_car</span>
                    <span class="hidden lg:block">Mis Vehículos</span>
                </a>
                <a href="logout.php" class="flex items-center gap-4 p-3 text-red-600 hover:bg-red-50 rounded-xl mt-10">
                    <span class="material-symbols-outlined">power_settings_new</span>
                    <span class="hidden lg:block">Cerrar Sesión</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <header class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-on-surface">Mis Vehículos</h1>
                <button class="bg-primary text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 hover:bg-blue-700 transition-all">
                    <span class="material-symbols-outlined">add</span>
                    Nuevo Vehículo
                </button>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($res_vehiculos && $res_vehiculos->num_rows > 0): ?>
                    <?php while($carro = $res_vehiculos->fetch_assoc()): ?>
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-outline-variant/20 relative overflow-hidden">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-3 bg-gray-100 rounded-2xl text-primary">
                                    <span class="material-symbols-outlined text-3xl">directions_car</span>
                                </div>
                                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-lg uppercase">Verificado</span>
                            </div>
                            <h3 class="text-xl font-bold text-on-surface"><?php echo $carro['placa']; ?></h3>
                            <p class="text-on-surface-variant text-sm"><?php echo $carro['marca'] . " " . $carro['modelo']; ?></p>
                            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">
                                <span class="text-xs text-gray-400">Registrado en: <?php echo $carro['fecha_registro']; ?></span>
                                <span class="material-symbols-outlined text-gray-400 hover:text-red-500 cursor-pointer">delete</span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full bg-white p-12 rounded-3xl border border-dashed border-outline-variant text-center">
                        <span class="material-symbols-outlined text-6xl text-gray-300">directions_car</span>
                        <p class="text-gray-500 mt-4">No tienes vehículos registrados aún.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>