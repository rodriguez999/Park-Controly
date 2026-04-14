<?php
require_once 'functions.php';
require_login();

// Datos del usuario para el saludo
$user = $_SESSION['user'];
$user_name = $user['nombre'];
$user_rol = $user['rol'] ?? 'usuario'; 

// 1. Obtener la configuración actual
$config = get_config($mysqli);
$capacidad_total = $config['capacidad_total'];

// 2. Vehículos actualmente en el parqueo
$res_activos = $mysqli->query("SELECT COUNT(*) as total FROM movimientos WHERE estado = 'EN_PARQUEO'");
$total_activos = $res_activos->fetch_assoc()['total'];

// 3. Calcular espacios disponibles
$espacios_disponibles = $capacidad_total - $total_activos;

// 4. Historial reciente (últimos 8 registros)
$res_historial = $mysqli->query("SELECT * FROM movimientos ORDER BY hora_entrada DESC LIMIT 8");
?>
<!doctype html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ParkControl - Dashboard</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
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
    <style>
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .font-headline { font-family: 'Manrope', sans-serif; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        main { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }

        .sidebar-text { white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity 0.2s, visibility 0.2s; }
        aside:hover .sidebar-text { opacity: 1; visibility: visible; }
        
        .timer-font { font-family: 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', monospace; }
    </style>
</head>
<body class="bg-surface-dim min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="pl-20 transition-all duration-300">
        <main class="flex-1 p-4 lg:p-10">
            
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="font-headline text-3xl font-black text-slate-900 tracking-tight italic">
                        ¡Hola, <?php echo explode(' ', $user_name)[0]; ?>! 👋
                    </h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest">
                            Sesión de <?php echo ($user_rol === 'admin' ? 'Administrador' : 'Operador'); ?> • ID #<?php echo str_pad($user['id'], 3, '0', STR_PAD_LEFT); ?>
                        </p>
                    </div>
                </div>
            </header>

            <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-8 rounded-[2.5rem] border border-outline-variant/10 shadow-sm relative overflow-hidden group">
                    <div class="absolute right-[-10px] top-[-10px] text-slate-50 opacity-10 group-hover:rotate-12 transition-transform duration-500">
                        <span class="material-symbols-outlined text-[120px]">grid_view</span>
                    </div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Capacidad Total</p>
                    <h3 class="text-5xl font-black text-slate-900 tracking-tighter"><?php echo $capacidad_total; ?></h3>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-outline-variant/10 shadow-sm relative overflow-hidden group">
                    <div class="absolute right-[-10px] top-[-10px] text-green-50 opacity-30 group-hover:scale-110 transition-transform duration-500">
                        <span class="material-symbols-outlined text-[120px]">check_circle</span>
                    </div>
                    <p class="text-green-600/60 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Espacios Libres</p>
                    <h3 class="text-5xl font-black text-green-600 tracking-tighter"><?php echo $espacios_disponibles; ?></h3>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-outline-variant/10 shadow-sm relative overflow-hidden group">
                    <div class="absolute right-[-10px] top-[-10px] text-orange-50 opacity-30 group-hover:translate-x-4 transition-transform duration-500">
                        <span class="material-symbols-outlined text-[120px]">directions_car</span>
                    </div>
                    <p class="text-orange-600/60 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Ocupación Actual</p>
                    <h3 class="text-5xl font-black text-orange-600 tracking-tighter"><?php echo $total_activos; ?></h3>
                </div>
            </section>

            <section class="bg-white rounded-[3rem] shadow-sm border border-outline-variant/20 overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/30">
                    <div>
                        <h3 class="font-headline font-black text-2xl text-slate-800 tracking-tight">Monitor en Tiempo Real</h3>
                        <p class="text-xs text-slate-400 font-medium italic">Seguimiento activo y registros recientes.</p>
                    </div>
                    <a href="historial.php" class="bg-primary text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 active:scale-95">
                        Historial Completo
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-slate-400 text-[10px] uppercase font-black tracking-[0.2em]">
                                <th class="px-8 py-6">Identificación</th>
                                <th class="px-8 py-6 text-center">Entrada</th>
                                <th class="px-8 py-6 text-center">Salida</th>
                                <th class="px-8 py-6 text-center">Tiempo / Duración</th>
                                <th class="px-8 py-6 text-right">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if ($res_historial->num_rows > 0): ?>
                                <?php while ($row = $res_historial->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/80 transition-all group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-primary group-hover:text-white transition-all duration-300">
                                                <span class="material-symbols-outlined text-2xl">minor_crash</span>
                                            </div>
                                            <div>
                                                <p class="font-black text-slate-800 text-lg tracking-widest leading-none mb-1"><?php echo strtoupper($row['placa']); ?></p>
                                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter"><?php echo $row['marca'] ?: 'Vehículo General'; ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-8 py-6 text-center">
                                        <span class="text-sm font-bold text-slate-600 block"><?php echo date('h:i A', strtotime($row['hora_entrada'])); ?></span>
                                        <span class="text-[10px] text-slate-300 font-black tracking-tighter"><?php echo date('d/m/y', strtotime($row['hora_entrada'])); ?></span>
                                    </td>

                                    <td class="px-8 py-6 text-center">
                                        <?php if ($row['hora_salida']): ?>
                                            <span class="text-sm font-bold text-slate-600 block"><?php echo date('h:i A', strtotime($row['hora_salida'])); ?></span>
                                            <span class="text-[10px] text-slate-300 font-black tracking-tighter"><?php echo date('d/m/y', strtotime($row['hora_salida'])); ?></span>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-300 italic">Pendiente</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-8 py-6 text-center">
                                        <?php if ($row['estado'] == 'EN_PARQUEO'): ?>
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-xl font-black text-primary timer-font tracking-tighter" 
                                                      data-time="<?php echo date('c', strtotime($row['hora_entrada'])); ?>">
                                                    00:00:00
                                                </span>
                                                <span class="text-[9px] font-black text-blue-300 uppercase tracking-widest">En vivo</span>
                                            </div>
                                        <?php else: 
                                            $entrada = new DateTime($row['hora_entrada']);
                                            $salida = new DateTime($row['hora_salida']);
                                            $intervalo = $entrada->diff($salida);
                                            $duracion = $intervalo->format('%H:%I:%S');
                                        ?>
                                            <div class="inline-flex flex-col items-center opacity-60">
                                                <span class="text-sm font-black text-slate-500 timer-font"><?php echo $duracion; ?></span>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">Total</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-8 py-6 text-right">
                                        <?php if ($row['estado'] == 'EN_PARQUEO'): ?>
                                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black bg-green-50 text-green-600 border border-green-100 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                                DENTRO
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black bg-slate-100 text-slate-400 border border-slate-200 uppercase">
                                                SALIDA
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-10 py-24 text-center">
                                        <div class="opacity-20 mb-4 text-slate-400">
                                            <span class="material-symbols-outlined text-7xl">nest_remote_comfort_sensor</span>
                                        </div>
                                        <p class="text-slate-400 font-black uppercase tracking-widest text-sm">Esperando registros...</p>
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

        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = window.location.pathname.split('/').pop() || 'menu.php';
            document.querySelectorAll('aside nav a').forEach(link => {
                if(link.getAttribute('href') === currentPath) {
                    link.classList.add('bg-primary/10', 'text-primary', 'font-bold');
                    link.classList.remove('text-gray-500');
                }
            });
        });
    </script>
</body>
</html>