<?php
require_once 'functions.php';
require_login();

$user_name = $_SESSION['usuario'];

// Consultamos todos los movimientos de la base de datos
$res_historial = $mysqli->query(
    'SELECT * FROM movimientos ORDER BY hora_entrada DESC',
);
?>
<!doctype html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ParkControl - Mi Historial</title>
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
                <a href="historial.php" class="flex items-center gap-4 p-3 bg-primary/10 text-primary rounded-xl font-semibold">
                    <span class="material-symbols-outlined">history</span>
                    <span class="hidden lg:block">Mi Historial</span>
                </a>
                <a href="logout.php" class="flex items-center gap-4 p-3 text-red-600 hover:bg-red-50 rounded-xl mt-10">
                    <span class="material-symbols-outlined">power_settings_new</span>
                    <span class="hidden lg:block">Cerrar Sesión</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-on-surface font-headline">Historial de Parqueos</h1>
                <p class="text-on-surface-variant">Revisa tus registros de entrada y salida</p>
            </header>

            <div class="bg-white rounded-3xl shadow-sm border border-outline-variant/20 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-on-surface-variant text-xs uppercase font-bold">
                            <tr>
                                <th class="px-6 py-4">Vehículo (Placa)</th>
                                <th class="px-6 py-4">Fecha/Hora Entrada</th>
                                <th class="px-6 py-4">Fecha/Hora Salida</th>
                                <th class="px-6 py-4">Monto Pagado</th>
                                <th class="px-6 py-4">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            <?php if (
                                $res_historial &&
                                $res_historial->num_rows > 0
                            ): ?>
                                <?php while (
                                    $row = $res_historial->fetch_assoc()
                                ): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-primary"><?php echo $row[
                                        'placa'
                                    ]; ?></td>
                                    <td class="px-6 py-4 text-sm"><?php echo $row[
                                        'hora_entrada'
                                    ]; ?></td>
                                    <td class="px-6 py-4 text-sm"><?php echo $row[
                                        'hora_salida'
                                    ] ?? '---'; ?></td>
                                    <td class="px-6 py-4 font-semibold text-on-surface">
                                        <?php echo $row['total_pago']
                                            ? '$' .
                                                number_format(
                                                    $row['total_pago'],
                                                    2,
                                                )
                                            : '$0.00'; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php $statusClass =
                                            $row['estado'] == 'EN_PARQUEO'
                                                ? 'bg-blue-100 text-blue-700'
                                                : 'bg-green-100 text-green-700'; ?>
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold <?php echo $statusClass; ?>">
                                            <?php echo $row['estado']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">No hay movimientos registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>