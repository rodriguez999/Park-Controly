<?php
require_once 'functions.php';
require_login(); // Si no está logueado, lo saca

// Obtenemos datos del usuario logueado
// Forma correcta de leer lo que guardaste en functions.php
$user_id = $_SESSION['user']['id'];
$user_name = $_SESSION['user']['nombre'];

// Consulta para estadísticas rápidas (Ejemplo: Vehículos actualmente en parqueo)
$res_activos = $mysqli->query("SELECT COUNT(*) as total FROM movimientos WHERE estado = 'EN_PARQUEO'");
$total_activos = $res_activos->fetch_assoc()['total'];

// Consulta para el historial reciente
$res_historial = $mysqli->query("SELECT * FROM movimientos ORDER BY hora_entrada DESC LIMIT 3");
?>
<!doctype html>
<html class="light" lang="es">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ParkControl - Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              'surface-dim': '#f1f3f9',
              'primary': '#005ac1',
              'on-surface': '#1a1c1e',
              'on-surface-variant': '#43474e',
              'outline-variant': '#c3c7cf',
            }
          }
        }
      }
    </script>
    <style>
      body { font-family: 'Inter', sans-serif; }
      .font-headline { font-family: 'Manrope', sans-serif; }
    </style>
  </head>
  <body class="bg-surface-dim min-h-screen">
    <div class="flex">
      <aside class="w-20 lg:w-64 bg-white min-h-screen border-r border-outline-variant/30 flex flex-col transition-all">
        <div class="p-6 flex items-center gap-3">
          <div class="bg-primary w-10 h-10 rounded-xl flex items-center justify-center text-white">
            <span class="material-symbols-outlined">local_parking</span>
          </div>
          <a class="font-headline font-bold text-xl hidden lg:block" href="menu.php">ParkControl</a>
        </div>
        
        <nav class="flex-1 mt-4 px-3 space-y-2">
          <a href="menu.php" class="flex items-center gap-4 p-3 bg-primary/10 text-primary rounded-xl font-semibold">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="hidden lg:block">Dashboard</span>
          </a>
          <a href="entrada.php" class="flex items-center gap-4 p-3 text-on-surface-variant hover:bg-gray-100 rounded-xl transition-all">
            <span class="material-symbols-outlined">login</span>
            <span class="hidden lg:block">Registrar Entrada</span>
          </a>
          <a href="salida.php" class="flex items-center gap-4 p-3 text-on-surface-variant hover:bg-gray-100 rounded-xl transition-all">
            <span class="material-symbols-outlined">logout</span>
            <span class="hidden lg:block">Registrar Salida</span>
          </a>
          <a href="logout.php" class="flex items-center gap-4 p-3 text-red-600 hover:bg-red-50 rounded-xl transition-all mt-10">
            <span class="material-symbols-outlined">power_settings_new</span>
            <span class="hidden lg:block">Cerrar Sesión</span>
          </a>
        </nav>
      </aside>

      <main class="flex-1 p-4 lg:p-8">
        <header class="flex justify-between items-center mb-8">
          <div>
            <h1 class="font-headline text-2xl lg:text-3xl font-bold text-on-surface">Bienvenido, <?php echo $user_name; ?></h1>
            <p class="text-on-surface-variant">Panel de control administrativo</p>
          </div>
          <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
              <p class="text-xs font-bold text-primary uppercase">Estado del Sistema</p>
              <p class="text-sm font-medium text-tertiary">En línea</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">
              <?php echo strtoupper(substr($user_name, 0, 1)); ?>
            </div>
          </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white p-6 rounded-3xl shadow-sm border border-outline-variant/20">
            <div class="flex justify-between items-start mb-4">
              <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                <span class="material-symbols-outlined">directions_car</span>
              </div>
              <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">ACTIVOS</span>
            </div>
            <h3 class="text-4xl font-black text-on-surface"><?php echo $total_activos; ?></h3>
            <p class="text-on-surface-variant text-sm mt-1">Vehículos en el parqueo ahora mismo</p>
          </div>

          <div class="bg-white p-6 rounded-3xl shadow-sm border border-outline-variant/20">
            <div class="flex justify-between items-start mb-4">
              <div class="p-3 bg-green-50 text-green-600 rounded-2xl">
                <span class="material-symbols-outlined">local_parking</span>
              </div>
            </div>
            <h3 class="text-4xl font-black text-on-surface">24</h3>
            <p class="text-on-surface-variant text-sm mt-1">Espacios disponibles de 50 totales</p>
          </div>

          <div class="bg-primary p-6 rounded-3xl shadow-lg text-white flex flex-col justify-between">
            <p class="font-bold">Acción Rápida</p>
            <a href="entrada.php" class="mt-4 bg-white text-primary font-bold py-3 px-4 rounded-xl text-center hover:bg-opacity-90 transition-all">
              Nueva Entrada
            </a>
          </div>
        </div>

        <section class="bg-white rounded-3xl shadow-sm border border-outline-variant/20 overflow-hidden">
          <div class="p-6 border-b border-outline-variant/10">
            <h3 class="font-headline font-bold text-lg">Últimos Movimientos</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead class="bg-gray-50 text-on-surface-variant text-xs uppercase font-bold">
                <tr>
                  <th class="px-6 py-4">Placa</th>
                  <th class="px-6 py-4">Entrada</th>
                  <th class="px-6 py-4">Estado</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/10">
                <?php while($row = $res_historial->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 font-bold text-primary"><?php echo $row['placa']; ?></td>
                  <td class="px-6 py-4 text-sm"><?php echo $row['hora_entrada']; ?></td>
                  <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold <?php echo $row['estado'] == 'EN_PARQUEO' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'; ?>">
                      <?php echo $row['estado']; ?>
                    </span>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </section>
      </main>
    </div>
  </body>
</html>