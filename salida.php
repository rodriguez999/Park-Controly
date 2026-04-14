<?php
require_once 'functions.php';
require_login();

// Configuración de zona horaria para RD
date_default_timezone_set('America/Santo_Domingo');

// Obtenemos datos del usuario logueado
$user_id = $_SESSION['user']['id'];
$user_name = $_SESSION['user']['nombre'];
$user_rol = $_SESSION['user']['rol'] ?? 'usuario';

$mensaje = '';
$detalles_pago = null;

// 1. BUSCAR VEHÍCULO PARA SALIDA
if (isset($_POST['buscar_placa'])) {
    $placa = strtoupper(trim($_POST['placa']));
    
    // Uso de sentencia preparada para buscar el vehículo
    $stmt_busqueda = $mysqli->prepare("SELECT * FROM movimientos WHERE placa = ? AND estado = 'EN_PARQUEO' LIMIT 1");
    $stmt_busqueda->bind_param('s', $placa);
    $stmt_busqueda->execute();
    $res = $stmt_busqueda->get_result();

    if ($res && $res->num_rows > 0) {
        $movimiento = $res->fetch_assoc();
        $entrada = new DateTime($movimiento['hora_entrada']);
        $ahora = new DateTime();
        $diferencia = $entrada->diff($ahora);

        // Cálculo de minutos totales para determinar horas
        $minutos_totales = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
        
        // Cobramos por hora empezada (ceil)
        $horas_a_cobrar = ceil($minutos_totales / 60);
        if ($horas_a_cobrar <= 0) $horas_a_cobrar = 1;

        // Obtener tarifa de la configuración dinámica
        $config = get_config($mysqli);
        $precio_por_hora = $config['tarifa_hora'] ?? 75;

        $total = $horas_a_cobrar * $precio_por_hora;

        $detalles_pago = [
            'id' => $movimiento['id'],
            'placa' => $placa,
            'entrada' => $movimiento['hora_entrada'],
            'tiempo' => ($minutos_totales < 60) ? $minutos_totales . ' min' : $horas_a_cobrar . ' hora(s)',
            'total' => $total,
        ];
    } else {
        $mensaje = '<div class="mb-6 p-4 rounded-2xl bg-orange-50 text-orange-700 font-bold border border-orange-100 flex items-center gap-3">
                        <span class="material-symbols-outlined">warning</span>
                        No se encontró un vehículo activo con la placa ' . htmlspecialchars($placa) . '.
                    </div>';
    }
}

// 2. CONFIRMAR SALIDA Y COBRO
if (isset($_POST['confirmar_pago'])) {
    $id_mov = $_POST['id_movimiento'];
    $monto = $_POST['monto'];

    $stmt = $mysqli->prepare(
        "UPDATE movimientos SET hora_salida = NOW(), total_pago = ?, estado = 'COMPLETADO' WHERE id = ?"
    );
    $stmt->bind_param('di', $monto, $id_mov);

    if ($stmt->execute()) {
        $mensaje = '
        <div class="mb-6 p-6 rounded-3xl bg-green-100 text-green-800 border border-green-200 shadow-sm animate-in fade-in slide-in-from-top duration-500">
            <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-green-600 text-3xl">check_circle</span>
                <span class="font-bold text-xl">¡Pago Procesado!</span>
            </div>
            <p class="mb-4 text-sm">El monto de <span class="font-bold text-lg text-green-900">RD$ ' . number_format($monto, 2) . '</span> ha sido registrado con éxito.</p>
            
            <a href="ticket_salida.php?id=' . $id_mov . '" target="_blank" 
               class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-green-700 transition-all shadow-md">
                <span class="material-symbols-outlined">print</span>
                Imprimir Recibo de Salida
            </a>
        </div>';
        $detalles_pago = null; 
    } else {
        $mensaje = '<div class="mb-6 p-4 rounded-2xl bg-red-100 text-red-700 font-bold border border-red-200">Error crítico al procesar el pago.</div>';
    }
}
?>
<!doctype html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ParkControl - Gestión de Salida</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: { 'surface-dim': '#f1f3f9', 'primary': '#005ac1', 'on-surface': '#1a1c1e', 'outline-variant': '#c3c7cf' }
          }
        }
      }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .font-headline { font-family: 'Manrope', sans-serif; }
        .sidebar-text { white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity 0.2s, visibility 0.2s; }
        aside:hover .sidebar-text { opacity: 1; visibility: visible; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        main { animation: fadeInUp 0.4s ease-out; }
    </style>
</head>
<body class="bg-surface-dim min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="pl-20 transition-all duration-300">
        <main class="p-4 lg:p-8">
            
            <header class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-[10px] font-black text-primary uppercase tracking-[0.2em] leading-none mb-1">Caja y Facturación</h2>
                    <p class="text-sm font-bold text-slate-500 uppercase tracking-tighter italic">Salida de Vehículos</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden md:block text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Terminal Activa</p>
                        <p class="text-xs font-bold text-slate-600 italic">ID-<?php echo str_pad($user_id, 3, '0', STR_PAD_LEFT); ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-white border border-outline-variant/30 flex items-center justify-center text-primary shadow-sm">
                        <span class="material-symbols-outlined">point_of_sale</span>
                    </div>
                </div>
            </header>

            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-10">
                    <h1 class="text-4xl font-black text-on-surface font-headline tracking-tight">Finalizar Estancia</h1>
                    <p class="text-slate-500 mt-2 italic font-medium">Calcula el tiempo y procesa el cobro del vehículo.</p>
                </div>

                <?php echo $mensaje; ?>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-outline-variant/20 mb-8">
                    <form action="salida.php" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Placa del Vehículo</label>
                            <input type="text" name="placa" id="inputPlaca" placeholder="A000000" required maxlength="8"
                                   class="w-full p-5 rounded-2xl border-2 border-slate-50 focus:border-primary outline-none uppercase font-mono text-2xl font-bold transition-all bg-slate-50 placeholder:text-slate-200"
                                   value="<?php echo isset($_POST['placa']) ? htmlspecialchars($_POST['placa']) : ''; ?>">
                        </div>
                        <button type="submit" name="buscar_placa" class="w-full md:w-auto bg-slate-900 text-white px-10 py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-2 group">
                            <span class="material-symbols-outlined group-hover:scale-110 transition-transform">search</span> BUSCAR
                        </button>
                    </form>
                </div>

                <?php if ($detalles_pago): ?>
                    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-outline-variant/10 overflow-hidden animate-in zoom-in duration-300">
                        <div class="bg-primary p-8 text-white flex justify-between items-center">
                            <div>
                                <p class="text-blue-100 text-[10px] font-black uppercase tracking-widest mb-1">Ticket de Salida</p>
                                <h2 class="text-4xl font-black font-mono tracking-tighter"><?php echo strtoupper($detalles_pago['placa']); ?></h2>
                            </div>
                            <div class="text-right">
                                <span class="material-symbols-outlined text-4xl opacity-50">receipt_long</span>
                            </div>
                        </div>
                        
                        <div class="p-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                                    <div class="flex items-center gap-3 mb-2 text-slate-400">
                                        <span class="material-symbols-outlined text-sm">schedule</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Hora de Entrada</span>
                                    </div>
                                    <p class="text-xl font-bold text-slate-700"><?php echo date('h:i A', strtotime($detalles_pago['entrada'])); ?></p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-1"><?php echo date('d M, Y', strtotime($detalles_pago['entrada'])); ?></p>
                                </div>
                                <div class="p-6 bg-blue-50/50 rounded-3xl border border-blue-100">
                                    <div class="flex items-center gap-3 mb-2 text-primary">
                                        <span class="material-symbols-outlined text-sm">timer</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Tiempo Transcurrido</span>
                                    </div>
                                    <p class="text-xl font-bold text-primary"><?php echo $detalles_pago['tiempo']; ?></p>
                                    <p class="text-[10px] text-blue-400 font-bold uppercase tracking-tighter mt-1 italic">Tarifa RD$ <?php echo number_format($precio_por_hora, 0); ?> / hr</p>
                                </div>
                            </div>
                            
                            <div class="flex flex-col md:flex-row justify-between items-center gap-8 pt-10 border-t border-dashed border-slate-200">
                                <div class="text-center md:text-left">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Total a cobrar</p>
                                    <p class="text-6xl font-black text-slate-900 tracking-tight">
                                        <span class="text-2xl font-bold text-slate-300 mr-1">RD$</span><?php echo number_format($detalles_pago['total'], 0); ?>
                                    </p>
                                </div>
                                
                                <form action="salida.php" method="POST" class="w-full md:w-auto">
                                    <input type="hidden" name="id_movimiento" value="<?php echo $detalles_pago['id']; ?>">
                                    <input type="hidden" name="monto" value="<?php echo $detalles_pago['total']; ?>">
                                    <button type="submit" name="confirmar_pago" 
                                            class="w-full bg-green-600 text-white px-12 py-6 rounded-3xl font-black hover:bg-green-700 transition-all shadow-2xl shadow-green-100 flex items-center justify-center gap-4 text-xs uppercase tracking-widest group">
                                        <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">payments</span>
                                        Confirmar Cobro
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Resaltar link activo en sidebar
        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = window.location.pathname.split('/').pop() || 'menu.php';
            document.querySelectorAll('aside nav a').forEach(link => {
                if(link.getAttribute('href') === currentPath) {
                    link.classList.add('bg-primary/10', 'text-primary', 'font-bold');
                    link.classList.remove('text-gray-500');
                }
            });
        });

        // Formateo de Placa
        const inputPlaca = document.getElementById('inputPlaca');
        if (inputPlaca) {
            inputPlaca.addEventListener('input', function(e) {
                let val = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                e.target.value = val;
            });
        }
    </script>
</body>
</html>