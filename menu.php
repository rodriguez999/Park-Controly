<?php
require_once 'functions.php';
require_login();

$user = $_SESSION['user'];
$user_name = $user['nombre'];

$config = get_config($mysqli);
$capacidad_total = $config['capacidad_total'];
$tarifa_hora = $config['tarifa_hora'] ?? 75;

$res_activos = $mysqli->query("SELECT COUNT(*) as total FROM movimientos WHERE estado = 'EN_PARQUEO'");
$total_activos = $res_activos->fetch_assoc()['total'];
$espacios_disponibles = $capacidad_total - $total_activos;

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
            colors: { 'surface-dim': '#f1f3f9', 'primary': '#005ac1', 'on-surface': '#1a1c1e', 'outline-variant': '#c3c7cf' }
          }
        }
      }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .font-headline { font-family: 'Manrope', sans-serif; }
        .spinner { width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        main { animation: fadeInUp 0.5s ease-out; }
    </style>
</head>
<body class="bg-surface-dim min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="pl-20">
        <main class="flex-1 p-4 lg:p-10">
            
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="font-headline text-3xl font-black text-slate-900 tracking-tight italic">¡Hola, <?php echo explode(' ', $user_name)[0]; ?>! 👋</h1>
                </div>
            </header>

            <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-8 rounded-[2.5rem] border border-outline-variant/10 shadow-sm relative overflow-hidden group">
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Capacidad Total</p>
                    <h3 class="text-5xl font-black text-slate-900 tracking-tighter"><?php echo $capacidad_total; ?></h3>
                    <div class="absolute right-6 bottom-6 opacity-5 group-hover:opacity-15 transition-all duration-500 translate-y-2 group-hover:translate-y-0">
                        <span class="material-symbols-outlined text-7xl">garage</span>
                    </div>
                </div>
                
                <div class="bg-white p-8 rounded-[2.5rem] border border-outline-variant/10 shadow-sm relative overflow-hidden group">
                    <p class="text-green-600/60 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Espacios Libres</p>
                    <h3 class="text-5xl font-black text-green-600 tracking-tighter"><?php echo $espacios_disponibles; ?></h3>
                    <div class="absolute right-6 bottom-6 opacity-5 group-hover:opacity-15 transition-all duration-500 translate-y-2 group-hover:translate-y-0">
                        <span class="material-symbols-outlined text-7xl">check_circle</span>
                    </div>
                </div>
                
                <div class="bg-white p-8 rounded-[2.5rem] border border-outline-variant/10 shadow-sm relative overflow-hidden group">
                    <p class="text-orange-600/60 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Ocupación Actual</p>
                    <h3 class="text-5xl font-black text-orange-600 tracking-tighter"><?php echo $total_activos; ?></h3>
                    <div class="absolute right-6 bottom-6 opacity-5 group-hover:opacity-15 transition-all duration-500 translate-y-2 group-hover:translate-y-0">
                        <span class="material-symbols-outlined text-7xl">directions_car</span>
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-[3rem] shadow-sm border border-outline-variant/20 overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                    <h3 class="font-headline font-black text-2xl text-slate-800 tracking-tight">Monitor de Estancia</h3>
                    <a href="historial.php" class="bg-primary text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg active:scale-95">Ver Todo</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-slate-400 text-[10px] uppercase font-black tracking-[0.2em]">
                                <th class="px-8 py-6">Vehículo</th>
                                <th class="px-8 py-6 text-center">Entrada</th>
                                <th class="px-8 py-6 text-center">Tiempo Vivo</th>
                                <th class="px-8 py-6 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php while ($row = $res_historial->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50/80 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-primary group-hover:text-white transition-all">
                                            <span class="material-symbols-outlined text-xl">minor_crash</span>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-800 text-lg tracking-widest leading-none mb-1"><?php echo strtoupper($row['placa']); ?></p>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase"><?php echo $row['marca'] ?: 'Vehículo'; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center text-sm font-bold text-slate-600">
                                    <?php echo date('h:i A', strtotime($row['hora_entrada'])); ?>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <?php if ($row['estado'] == 'EN_PARQUEO'): ?>
                                        <span class="text-xl font-black text-primary tracking-tighter" data-time="<?php echo date('c', strtotime($row['hora_entrada'])); ?>">00:00:00</span>
                                    <?php else: ?>
                                        <span class="text-xs font-black text-slate-300 uppercase tracking-widest italic">Finalizado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <?php if ($row['estado'] == 'EN_PARQUEO'): ?>
                                        <button onclick="prepararCobro(<?php echo htmlspecialchars(json_encode($row)); ?>, <?php echo $tarifa_hora; ?>)" 
                                                class="px-6 py-2 rounded-xl text-[10px] font-black bg-green-500 text-white hover:bg-green-600 transition-all uppercase shadow-md shadow-green-100">
                                            Pagar Salida
                                        </button>
                                    <?php else: ?>
                                        <a href="ticket_salida.php?id=<?php echo $row['id']; ?>" target="_blank" class="px-6 py-2 rounded-xl text-[10px] font-black bg-slate-100 text-slate-400 border border-slate-200 uppercase hover:bg-slate-200 transition-all">Reimprimir</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- MODAL DE COBRO UNIFICADO -->
    <div id="modalCobro" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 w-full max-w-md overflow-hidden relative">
            
            <div id="pantalla_detalles">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-headline font-black text-xl text-slate-800 italic">Resumen de Cuenta</h3>
                    <button onclick="cerrarModales()" class="text-slate-300 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
                </div>
                <div class="bg-slate-50 rounded-3xl p-6 mb-8 border border-slate-100">
                    <div class="flex justify-between mb-4 items-center">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Vehículo</span>
                        <span id="det_placa" class="font-black text-slate-800 text-xl tracking-widest">---</span>
                    </div>
                    <div class="border-t border-dashed border-slate-200 pt-5 flex justify-between items-center">
                        <span class="text-[10px] font-black text-primary uppercase tracking-widest">Total</span>
                        <span class="text-4xl font-black text-slate-900 leading-none">RD$ <span id="det_total">0</span></span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="abrirConfirmacionEfectivo()" class="p-6 rounded-[2rem] border-2 border-slate-100 hover:border-green-500 hover:bg-green-50 transition-all group text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300 group-hover:text-green-500 mb-2">payments</span>
                        <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Efectivo</p>
                    </button>
                    <button onclick="irAPasarela()" class="p-6 rounded-[2rem] border-2 border-slate-100 hover:border-primary hover:bg-blue-50 transition-all group text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300 group-hover:text-primary mb-2">credit_card</span>
                        <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Tarjeta</p>
                    </button>
                </div>
            </div>

            <div id="pantalla_confirmacion" class="hidden text-center py-4">
                <span class="material-symbols-outlined text-5xl text-orange-500 mb-4">help_center</span>
                <h3 class="font-black text-xl text-slate-800 mb-2">¿Confirmar Pago?</h3>
                <p class="text-slate-500 text-sm mb-8 px-6">Vas a registrar el cobro en efectivo por valor de <b class="text-slate-800">RD$ <span id="conf_monto">0</span></b></p>
                <div class="flex gap-3">
                    <button onclick="volverADetalles()" class="flex-1 py-4 rounded-2xl text-[10px] font-black uppercase text-slate-400 bg-slate-50 hover:bg-slate-100 transition-all">Cancelar</button>
                    <button onclick="confirmarPagoFinal(null, 'EFECTIVO')" class="flex-1 py-4 rounded-2xl text-[10px] font-black uppercase text-white bg-green-500 hover:bg-green-600 shadow-lg shadow-green-100 transition-all">Sí, Procesar</button>
                </div>
            </div>

            <div id="pantalla_tarjeta" class="hidden">
                <button onclick="volverADetalles()" class="text-primary text-[10px] font-black mb-6 flex items-center gap-1 uppercase tracking-widest"><span class="material-symbols-outlined text-sm">arrow_back</span> Atrás</button>
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-6 text-white mb-8 shadow-xl h-44 flex flex-col justify-between relative overflow-hidden">
                    <div class="relative z-10 flex justify-between items-start">
                        <div class="w-10 h-7 bg-yellow-500/80 rounded-md"></div>
                        <span class="text-[8px] opacity-40 uppercase tracking-widest font-black italic">ParkControl Pay</span>
                    </div>
                    <div class="relative z-10 text-lg tracking-[0.25em] font-mono" id="card_preview">**** **** **** ****</div>
                    <div class="relative z-10 flex justify-between items-end">
                        <div><p class="text-[7px] opacity-40 uppercase font-bold">Titular</p><p class="text-[10px] font-bold uppercase"><?php echo $user_name; ?></p></div>
                        <div class="text-right"><p class="text-[7px] opacity-40 uppercase font-bold">Expira</p><p class="text-[10px] font-bold" id="expiry_preview">00/00</p></div>
                    </div>
                </div>
                <form onsubmit="confirmarPagoFinal(event, 'TARJETA')" class="space-y-3">
                    <input type="text" id="card_input" maxlength="19" placeholder="Número de Tarjeta" required class="w-full p-4 bg-slate-50 rounded-2xl text-sm font-bold tracking-widest border-none">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" id="expiry_input" maxlength="5" placeholder="MM/AA" required class="w-full p-4 bg-slate-50 rounded-2xl text-center text-sm font-bold border-none">
                        <input type="password" maxlength="3" placeholder="CVC" required class="w-full p-4 bg-slate-50 rounded-2xl text-center text-sm font-bold border-none">
                    </div>
                    <button type="submit" id="btn_pagar_tarjeta" class="w-full bg-primary text-white py-5 rounded-[2rem] font-black uppercase text-[10px] tracking-widest shadow-xl mt-4 transition-all">Procesar Tarjeta</button>
                </form>
            </div>

            <div id="pantalla_exito" class="hidden text-center py-6">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6"><span class="material-symbols-outlined text-5xl">check_circle</span></div>
                <h3 class="font-black text-2xl text-slate-800 mb-2">¡Pago Exitoso!</h3>
                <p class="text-slate-500 text-sm mb-10 px-4">El vehículo ha sido liberado del sistema.</p>
                <a id="link_factura" href="#" target="_blank" class="block w-full bg-slate-900 text-white py-5 rounded-[2rem] font-black uppercase text-[10px] tracking-widest shadow-2xl flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined text-xl">print</span> Imprimir Recibo
                </a>
                <button onclick="window.location.reload()" class="mt-6 text-[10px] font-black text-slate-300 uppercase hover:text-primary tracking-[0.2em] transition-colors">Volver al Dashboard</button>
            </div>
        </div>
    </div>

    <script>
        let datosMovimientoActual = null;
        let montoTotal = 0;

        function prepararCobro(movimiento, tarifa) {
            datosMovimientoActual = movimiento;
            const entrada = new Date(movimiento.hora_entrada);
            const ahora = new Date();
            const diffMins = Math.floor((ahora - entrada) / 60000);
            const horasCobrar = Math.max(1, Math.ceil(diffMins / 60));
            montoTotal = horasCobrar * tarifa;

            document.getElementById('det_placa').innerText = movimiento.placa.toUpperCase();
            document.getElementById('det_total').innerText = montoTotal.toLocaleString();
            document.getElementById('conf_monto').innerText = montoTotal.toLocaleString();
            
            abrirPantalla('pantalla_detalles');
            document.getElementById('modalCobro').classList.remove('hidden');
        }

        function abrirConfirmacionEfectivo() { abrirPantalla('pantalla_confirmacion'); }
        function irAPasarela() { abrirPantalla('pantalla_tarjeta'); }
        function volverADetalles() { abrirPantalla('pantalla_detalles'); }
        function cerrarModales() { document.getElementById('modalCobro').classList.add('hidden'); }

        function abrirPantalla(id) {
            const pantallas = ['pantalla_detalles', 'pantalla_confirmacion', 'pantalla_tarjeta', 'pantalla_exito'];
            pantallas.forEach(p => document.getElementById(p).classList.add('hidden'));
            document.getElementById(id).classList.remove('hidden');
        }

        async function confirmarPagoFinal(event, metodo) {
            if(event) event.preventDefault();
            
            const btn = document.getElementById('btn_pagar_tarjeta');
            if(metodo === 'TARJETA') {
                btn.disabled = true;
                btn.innerHTML = `<div class="spinner mx-auto"></div>`;
            }

            try {
                const response = await fetch('procesar_pago.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        id: datosMovimientoActual.id, 
                        metodo: metodo,
                        monto: montoTotal
                    })
                });

                // Leemos la respuesta como texto primero para detectar errores de PHP no controlados
                const rawResponse = await response.text();
                
                try {
                    const data = JSON.parse(rawResponse);
                    if(data.status === 'success') {
                        abrirPantalla('pantalla_exito');
                        document.getElementById('link_factura').href = `ticket_salida.php?id=${datosMovimientoActual.id}`;
                    } else {
                        alert("Error del servidor: " + (data.message || "Error desconocido"));
                        if(metodo === 'TARJETA') {
                            btn.disabled = false;
                            btn.innerHTML = "Procesar Tarjeta";
                        }
                    }
                } catch (e) {
                    console.error("Respuesta no válida del servidor:", rawResponse);
                    alert("El servidor envió una respuesta inválida. Revisa la consola (F12).");
                }

            } catch (e) {
                alert("Error crítico de comunicación. Verifica que el archivo procesar_pago.php exista.");
                console.error(e);
            }
        }

        setInterval(() => {
            document.querySelectorAll('[data-time]').forEach(timer => {
                const start = new Date(timer.getAttribute('data-time')).getTime();
                const diff = new Date().getTime() - start;
                const h = Math.floor(diff / 3600000).toString().padStart(2,'0');
                const m = Math.floor((diff % 3600000) / 60000).toString().padStart(2,'0');
                const s = Math.floor((diff % 60000) / 1000).toString().padStart(2,'0');
                timer.innerText = `${h}:${m}:${s}`;
            });
        }, 1000);

        document.getElementById('card_input').addEventListener('input', (e) => {
            let v = e.target.value.replace(/\D/g, '').match(/.{1,4}/g)?.join(' ') || '';
            e.target.value = v;
            document.getElementById('card_preview').innerText = v || "**** **** **** ****";
        });
        document.getElementById('expiry_input').addEventListener('input', (e) => {
            let v = e.target.value.replace(/\D/g, '');
            if(v.length > 2) v = v.substring(0,2) + '/' + v.substring(2,4);
            e.target.value = v;
            document.getElementById('expiry_preview').innerText = v || "00/00";
        });
    </script>
</body>
</html>