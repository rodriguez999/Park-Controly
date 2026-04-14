<?php
require_once 'functions.php';
require_admin(); // Protección: Solo admin

// 1. Total recaudado (Suma de total_pago)
$res_dinero = $mysqli->query("SELECT SUM(total_pago) as total FROM movimientos WHERE estado = 'COMPLETADO'");
$total_recaudado = $res_dinero->fetch_assoc()['total'] ?? 0;

// 2. Total de vehículos hoy
$hoy = date('Y-m-d');
$res_hoy = $mysqli->query("SELECT COUNT(*) as total FROM movimientos WHERE DATE(hora_entrada) = '$hoy'");
$vehiculos_hoy = $res_hoy->fetch_assoc()['total'];

// 3. Top 5 vehículos que más frecuentan (Opcional)
$res_top = $mysqli->query("SELECT placa, COUNT(*) as visitas FROM movimientos GROUP BY placa ORDER BY visitas DESC LIMIT 5");
?>
<!doctype html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ParkControl - Reportes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
</head>
<body class="bg-[#f1f3f9] min-h-screen">
    <div class="flex">
        <aside class="w-20 lg:w-64 bg-white min-h-screen border-r border-gray-200 flex flex-col">
            <div class="p-6 flex items-center gap-3">
                <div class="bg-[#005ac1] w-10 h-10 rounded-xl flex items-center justify-center text-white">
                    <a class="material-symbols-outlined" href="menu.php">local_parking</a>
                </div>
                <span class="font-bold text-xl hidden lg:block">ParkControl</span>
            </div>
            <nav class="flex-1 mt-4 px-3 space-y-2">
                <a href="menu.php" class="flex items-center gap-4 p-3 text-gray-600 hover:bg-gray-100 rounded-xl">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span class="hidden lg:block">Dashboard</span>
                </a>
                <a href="reportes.php" class="flex items-center gap-4 p-3 bg-blue-50 text-[#005ac1] rounded-xl font-semibold">
                    <span class="material-symbols-outlined">analytics</span>
                    <span class="hidden lg:block">Reportes</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Reportes de Ingresos</h1>
                <p class="text-gray-500">Resumen financiero y de flujo de vehículos</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-2">Total Recaudado (Histórico)</p>
                    <h2 class="text-5xl font-black text-green-600">RD$ <?php echo number_format($total_recaudado, 2); ?></h2>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-2">Vehículos Registrados Hoy</p>
                    <h2 class="text-5xl font-black text-[#005ac1]"><?php echo $vehiculos_hoy; ?></h2>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="font-bold text-lg">Vehículos más frecuentes</h3>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-400 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="px-6 py-4">Placa</th>
                            <th class="px-6 py-4">Cantidad de Visitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php while($row = $res_top->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 font-bold text-blue-600"><?php echo $row['placa']; ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo $row['visitas']; ?> veces</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>