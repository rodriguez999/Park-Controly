<?php
require_once 'functions.php';

// Verificación de seguridad: Solo clientes
if (!is_logged_in() || $_SESSION['user']['rol'] !== 'cliente') {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

// 1. Obtener filtros de búsqueda
$search = $_GET['search'] ?? '';
$where_clause = "WHERE usuario_id = $user_id"; // Filtro obligatorio por seguridad

if (!empty($search)) {
    $search = $mysqli->real_escape_string($search);
    $where_clause .= " AND (placa LIKE '%$search%' OR marca LIKE '%$search%')";
}

// 2. Consulta de historial personalizada
$query = "SELECT * FROM movimientos $where_clause ORDER BY hora_entrada DESC";
$res_historial = $mysqli->query($query);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mi Historial - ParkControl</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              'surface-dim': '#f8fafc',
              'primary': '#005ac1',
              'outline-variant': '#c3c7cf',
            }
          }
        }
      }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-headline { font-family: 'Manrope', sans-serif; }
        .timer-font { font-family: 'ui-monospace', monospace; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        main { animation: fadeInUp 0.4s ease-out; }

        @media (max-width: 768px) {
            .hide-mobile { display: none; }
        }
    </style>
</head>
<body class="bg-surface-dim min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="pl-20 transition-all duration-300">
        <main class="flex-1 p-4 lg:p-10 max-w-7xl mx-auto">
            
            <!-- HEADER -->
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <h2 class="text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-1">Actividad</h2>
                    <h1 class="font-headline text-4xl font-black text-slate-900 tracking-tight">Mi Historial</h1>
                    <p class="text-slate-400 text-xs font-medium mt-1">Consulta tus registros de entrada y salida.</p>
                </div>

                <form action="" method="GET" class="relative group w-full md:w-80">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-colors">search</span>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Buscar por placa o marca..." 
                           class="w-full pl-12 pr-4 py-4 bg-white border border-outline-variant/30 rounded-2xl text-sm font-medium focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all shadow-sm">
                </form>
            </header>

            <!-- TABLA DE MOVIMIENTOS -->
            <section class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-slate-400 text-[10px] uppercase font-black tracking-widest bg-slate-50/50">
                                <th class="px-8 py-6">Vehículo</th>
                                <th class="px-8 py-6 text-center">Entrada</th>
                                <th class="px-8 py-6 text-center hide-mobile">Salida</th>
                                <th class="px-8 py-6 text-center">Duración</th>
                                <th class="px-8 py-6 text-center">Monto</th>
                                <th class="px-8 py-6 text-right">Recibo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if ($res_historial && $res_historial->num_rows > 0): ?>
                                <?php while ($row = $res_historial->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/50 transition-all group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-11 h-11 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-primary group-hover:bg-primary/5 transition-all">
                                                <span class="material-symbols-outlined">directions_car</span>
                                            </div>
                                            <div>
                                                <p class="font-black text-slate-800 tracking-wider leading-none mb-1 text-base"><?php echo strtoupper($row['placa']); ?></p>
                                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter"><?php echo $row['marca'] ?: 'MI VEHÍCULO'; ?></p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6 text-center">
                                        <span class="text-sm font-bold text-slate-700 block mb-0.5"><?php echo date('h:i A', strtotime($row['hora_entrada'])); ?></span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter"><?php echo date('d M, Y', strtotime($row['hora_entrada'])); ?></span>
                                    </td>

                                    <td class="px-8 py-6 text-center hide-mobile">
                                        <?php if ($row['hora_salida']): ?>
                                            <span class="text-sm font-bold text-slate-700 block mb-0.5"><?php echo date('h:i A', strtotime($row['hora_salida'])); ?></span>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter"><?php echo date('d M, Y', strtotime($row['hora_salida'])); ?></span>
                                        <?php else: ?>
                                            <span class="text-[10px] text-primary font-black italic uppercase tracking-widest animate-pulse">En Parqueo</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-8 py-6 text-center">
                                        <?php if ($row['estado'] == 'EN_PARQUEO'): ?>
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-lg font-black text-primary timer-font tracking-tighter" 
                                                      data-time="<?php echo date('c', strtotime($row['hora_entrada'])); ?>">
                                                    00:00:00
                                                </span>
                                            </div>
                                        <?php else: 
                                            $entrada = new DateTime($row['hora_entrada']);
                                            $salida = new DateTime($row['hora_salida']);
                                            $intervalo = $entrada->diff($salida);
                                            echo '<span class="text-sm font-black text-slate-500 timer-font">'.$intervalo->format('%H:%I:%S').'</span>';
                                        endif; ?>
                                    </td>

                                    <td class="px-8 py-6 text-center">
                                        <!-- CORRECCIÓN: Se agrega ?? 0 para evitar el error de índice indefinido -->
                                        <p class="text-sm font-black text-slate-900">RD$ <?php echo number_format($row['monto'] ?? 0, 2); ?></p>
                                        <span class="text-[9px] font-black uppercase <?php echo $row['estado'] == 'FINALIZADO' ? 'text-emerald-500' : 'text-amber-500'; ?>">
                                            <?php echo ($row['estado'] == 'EN_PARQUEO') ? 'Pendiente' : 'Pagado'; ?>
                                        </span>
                                    </td>

                                    <td class="px-8 py-6 text-right">
                                        <a href="ticket.php?id=<?php echo $row['id']; ?>" target="_blank" 
                                           class="w-10 h-10 inline-flex items-center justify-center rounded-xl text-slate-300 hover:text-primary hover:bg-primary/5 transition-all">
                                            <span class="material-symbols-outlined text-xl">receipt_long</span>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-8 py-32 text-center">
                                        <div class="flex flex-col items-center gap-4 opacity-20">
                                            <span class="material-symbols-outlined text-8xl text-slate-300">history_toggle_off</span>
                                            <p class="text-slate-900 font-black uppercase tracking-[0.3em] text-sm">No hay registros</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        function actualizarCronometros() {
            const timers = document.querySelectorAll('[data-time]');
            timers.forEach(timer => {
                const start = new Date(timer.getAttribute('data-time')).getTime();
                const now = new Date().getTime();
                const diff = now - start;

                if (diff > 0) {
                    const h = Math.floor(diff / 3600000);
                    const m = Math.floor((diff % 3600000) / 60000);
                    const s = Math.floor((diff % 60000) / 1000);

                    timer.innerText = 
                        (h < 10 ? "0" + h : h) + ":" + 
                        (m < 10 ? "0" + m : m) + ":" + 
                        (s < 10 ? "0" + s : s);
                }
            });
        }

        setInterval(actualizarCronometros, 1000);
        actualizarCronometros();
    </script>
</body>
</html>