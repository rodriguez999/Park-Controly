<?php
// ©️Bryan Rodriguez Abad - 100523553
require_once 'functions.php';

// 1. Verificación de seguridad
if (!is_logged_in() || $_SESSION['user']['rol'] !== 'cliente') {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$nombre_usuario = $_SESSION['user']['nombre'];

// 2. Obtener tarifa por hora desde la configuración
$config = get_config($mysqli);
$tarifa_hora = $config['tarifa_hora'] ?? 75;

// 3. Consulta de datos del perfil
$query = "SELECT nombre, correo, username FROM usuarios WHERE id = ? LIMIT 1";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();

// 4. Lógica de Vehículos en Parqueo y Monto Acumulado
$vehiculos_dentro = 0;
$monto_acumulado_total = 0;

try {
    $query_activos = "SELECT hora_entrada FROM movimientos WHERE usuario_id = ? AND estado = 'EN_PARQUEO'";
    $stmt_a = $mysqli->prepare($query_activos);
    $stmt_a->bind_param('i', $user_id);
    $stmt_a->execute();
    $res_activos = $stmt_a->get_result();
    $vehiculos_dentro = $res_activos->num_rows;

    while ($mov = $res_activos->fetch_assoc()) {
        $fecha_entrada = new DateTime($mov['hora_entrada']);
        $ahora = new DateTime();
        $diferencia = $fecha_entrada->diff($ahora);
        
        $horas = $diferencia->h + ($diferencia->days * 24);
        if ($diferencia->i > 0 || $horas == 0) $horas++; 
        
        $monto_acumulado_total += ($horas * $tarifa_hora);
    }
} catch (Exception $e) { 
    $vehiculos_dentro = 0;
    $monto_acumulado_total = 0; 
}

// 5. Consulta de Actividad Reciente (Historial)
$res_historial = false;
try {
    $stmt_h = $mysqli->prepare("SELECT placa, hora_entrada, monto, estado FROM movimientos WHERE usuario_id = ? ORDER BY hora_entrada DESC LIMIT 3");
    $stmt_h->bind_param('i', $user_id);
    $stmt_h->execute();
    $res_historial = $stmt_h->get_result();
} catch (Exception $e) { $res_historial = false; }

// 6. Consulta de Mis Vehículos Registrados
$lista_vehiculos = [];
try {
    $stmt_v = $mysqli->prepare("SELECT id, placa, marca, modelo FROM vehiculos WHERE usuario_id = ?");
    $stmt_v->bind_param('i', $user_id);
    $stmt_v->execute();
    $res_v = $stmt_v->get_result();
    while($row = $res_v->fetch_assoc()) {
        $lista_vehiculos[] = $row;
    }
} catch (Exception $e) { $lista_vehiculos = []; }
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mi Panel - ParkControl</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Material+Symbols+Outlined:wght@200..700" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .fade-up { animation: fadeUp 0.5s ease-out forwards; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Estilos para el Modal */
        #modalSalida.hidden { display: none; }
        #modalSalida.flex { display: flex; }
    </style>
</head>
<body class="min-h-screen flex text-slate-900">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 ml-20 p-8 transition-all duration-300">
        <div class="max-w-5xl mx-auto">
            
            <!-- HEADER -->
            <header class="mb-10 fade-up" style="animation-delay: 0.1s;">
                <h2 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-1">Panel de Cliente</h2>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter italic">¡Hola, <?php echo htmlspecialchars(explode(' ', $nombre_usuario)[0]); ?>! 👋</h1>
                <p class="text-slate-400 text-sm font-medium">Gestiona tus entradas y vehículos registrados.</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-10">
                    <!-- TARJETA DE BALANCE REAL -->
                    <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden fade-up" style="animation-delay: 0.2s;">
                        <div class="relative z-10">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-slate-400 text-[11px] font-black uppercase tracking-[0.2em] mb-3">Total Estimado a Pagar</p>
                                    <h3 class="text-6xl font-black mb-8 italic tracking-tighter">
                                        RD$ <?php echo number_format($monto_acumulado_total, 2); ?>
                                    </h3>
                                </div>
                                <div class="text-right">
                                    <p class="text-slate-400 text-[11px] font-black uppercase tracking-[0.2em] mb-3">En Parqueo</p>
                                    <h4 class="text-4xl font-black text-blue-400 leading-none"><?php echo $vehiculos_dentro; ?></h4>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <?php if($vehiculos_dentro > 0): ?>
                                    <span class="px-6 py-2 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-full text-xs font-black uppercase tracking-wider">Servicio en curso</span>
                                <?php else: ?>
                                    <span class="px-6 py-2 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full text-xs font-black uppercase tracking-wider italic">Sin vehículos activos</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="material-symbols-outlined absolute -right-8 -bottom-8 text-white/5 text-[15rem] pointer-events-none">payments</span>
                    </div>

                    <!-- SECCIÓN: MIS VEHÍCULOS -->
                    <div class="space-y-4 fade-up" style="animation-delay: 0.3s;">
                        <div class="flex justify-between items-center px-2">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mis Vehículos</h4>
                            <a href="entrada.php" class="flex items-center gap-1 text-[10px] font-black text-blue-600 uppercase hover:underline outline-none">
                                <span class="material-symbols-outlined text-sm">add_circle</span> Nueva Entrada
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php if(!empty($lista_vehiculos)): ?>
                                <?php foreach($lista_vehiculos as $v): 
                                    $stmt_check = $mysqli->prepare("SELECT id FROM movimientos WHERE placa = ? AND estado = 'EN_PARQUEO' LIMIT 1");
                                    $stmt_check->bind_param('s', $v['placa']);
                                    $stmt_check->execute();
                                    $esta_dentro = $stmt_check->get_result()->num_rows > 0;
                                ?>
                                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between group transition-all hover:shadow-md">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 border border-slate-100 transition-colors">
                                                <span class="material-symbols-outlined text-3xl">directions_car</span>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest"><?php echo htmlspecialchars($v['marca'] . " " . $v['modelo']); ?></p>
                                                <p class="text-lg font-black text-slate-800 tracking-tight"><?php echo htmlspecialchars($v['placa']); ?></p>
                                            </div>
                                        </div>

                                        <div class="flex gap-2">
                                            <?php if($esta_dentro): ?>
                                                <!-- BOTÓN DE SALIDA MODIFICADO -->
                                                <button onclick="confirmarSalida('<?php echo $v['placa']; ?>')" 
                                                   class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm shadow-red-100"
                                                   title="Registrar Salida">
                                                    <span class="material-symbols-outlined">logout</span>
                                                </button>
                                            <?php else: ?>
                                                <a href="entrada.php?placa=<?php echo urlencode($v['placa']); ?>" 
                                                   class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all shadow-sm shadow-blue-100"
                                                   title="Nueva Entrada">
                                                    <span class="material-symbols-outlined">login</span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-span-2 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2rem] p-10 text-center">
                                    <p class="text-slate-400 text-xs font-medium">Aún no tienes vehículos guardados.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- SECCIÓN: ACTIVIDAD RECIENTE -->
                    <div class="space-y-4 fade-up" style="animation-delay: 0.4s;">
                        <div class="flex justify-between items-center px-2">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Últimos Movimientos</h4>
                        </div>
                        <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm border border-slate-100">
                            <?php if($res_historial && $res_historial->num_rows > 0): ?>
                                <?php while($h = $res_historial->fetch_assoc()): ?>
                                    <div class="p-6 border-b border-slate-50 flex justify-between items-center hover:bg-slate-50/50 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                                                <span class="material-symbols-outlined text-xl">history</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-slate-800"><?php echo htmlspecialchars($h['placa']); ?></p>
                                                <p class="text-[10px] text-slate-400 font-bold uppercase"><?php echo date('d M, Y', strtotime($h['hora_entrada'])); ?></p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-black text-slate-900">RD$ <?php echo number_format($h['monto'] ?? 0, 2); ?></p>
                                            <span class="text-[8px] font-black uppercase px-3 py-1 rounded-full <?php echo $h['estado'] == 'EN_PARQUEO' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600'; ?>">
                                                <?php echo ($h['estado'] == 'EN_PARQUEO') ? 'Pendiente' : 'Pagado'; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="p-12 text-center text-slate-300 italic text-xs">Sin actividad reciente registrada.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- COLUMNA DERECHA -->
                <div class="space-y-6 fade-up" style="animation-delay: 0.5s;">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Información de Perfil</h4>
                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 space-y-6">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Username</p>
                                <p class="text-sm font-bold text-slate-800">@<?php echo htmlspecialchars($datos['username']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400">
                                <span class="material-symbols-outlined">mail</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Correo</p>
                                <p class="text-sm font-bold text-slate-800 break-all"><?php echo htmlspecialchars($datos['correo']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- MODAL PERSONALIZADO DE SALIDA -->
    <div id="modalSalida" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="cerrarModal()"></div>
        
        <!-- Contenido del Modal -->
        <div class="relative bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl text-center transform transition-all scale-95 opacity-0 duration-300" id="modalContent">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-4xl">logout</span>
            </div>
            
            <h3 class="text-2xl font-black text-slate-900 italic tracking-tighter mb-2">Registrar Salida</h3>
            <p class="text-slate-500 text-sm font-medium mb-8">¿Confirmas que el vehículo con placa <span id="placaModal" class="text-slate-900 font-black"></span> está saliendo del parqueo ahora?</p>
            
            <div class="flex flex-col gap-3">
                <a id="btnConfirmarSalida" href="#" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition-all">
                    Confirmar Salida
                </a>
                <button onclick="cerrarModal()" class="w-full bg-slate-50 text-slate-400 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-100 transition-all">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmarSalida(placa) {
            const modal = document.getElementById('modalSalida');
            const content = document.getElementById('modalContent');
            const placaSpan = document.getElementById('placaModal');
            const btnConfirmar = document.getElementById('btnConfirmarSalida');

            placaSpan.innerText = placa;
            btnConfirmar.href = `salida.php?placa=${encodeURIComponent(placa)}`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Animación de entrada
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function cerrarModal() {
            const modal = document.getElementById('modalSalida');
            const content = document.getElementById('modalContent');

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }
    </script>
</body>
</html>