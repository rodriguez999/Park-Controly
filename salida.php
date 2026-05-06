<?php
// ©️Bryan Rodriguez Abad - 100523553
require_once 'functions.php';
require_login();

// Configuración de zona horaria para RD
date_default_timezone_set('America/Santo_Domingo');

$user_id = $_SESSION['user']['id'];
$user_name = $_SESSION['user']['nombre'];

$mensaje = '';
$detalles_pago = null;

// 1. CAPTURAR PLACA (Ya sea por POST al buscar o por GET desde el Dashboard)
$placa_prellenada = '';
if (isset($_POST['placa'])) {
    $placa_prellenada = strtoupper(trim($_POST['placa']));
} elseif (isset($_GET['placa'])) {
    $placa_prellenada = strtoupper(trim($_GET['placa']));
}

// 2. BUSCAR VEHÍCULO PARA SALIDA
// Se ejecuta si se presiona el botón o si viene una placa por GET (vía script JS al final)
if (isset($_POST['buscar_placa'])) {
    $placa = strtoupper(trim($_POST['placa']));
    
    $stmt_busqueda = $mysqli->prepare("SELECT * FROM movimientos WHERE placa = ? AND estado = 'EN_PARQUEO' LIMIT 1");
    $stmt_busqueda->bind_param('s', $placa);
    $stmt_busqueda->execute();
    $res = $stmt_busqueda->get_result();

    if ($res && $res->num_rows > 0) {
        $movimiento = $res->fetch_assoc();
        $entrada = new DateTime($movimiento['hora_entrada']);
        $ahora = new DateTime();
        $diferencia = $entrada->diff($ahora);

        $minutos_totales = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
        $horas_a_cobrar = ceil($minutos_totales / 60);
        
        if ($horas_a_cobrar <= 0) $horas_a_cobrar = 1;

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
        $mensaje = '
        <div class="mb-6 p-4 rounded-2xl bg-orange-50 text-orange-700 font-bold border border-orange-100 flex items-center gap-3 animate-pulse">
            <span class="material-symbols-outlined">warning</span>
            No se encontró un vehículo activo con la placa ' . htmlspecialchars($placa) . '.
        </div>';
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
        .spinner { width: 24px; height: 24px; border: 3px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        main { animation: fadeInUp 0.4s ease-out; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-surface-dim min-h-screen">

    <?php include 'sidebar.php'; ?>

    <div class="pl-20 transition-all duration-300">
        <main class="p-4 lg:p-8">
            
            <header class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-1">Caja y Facturación</h2>
                    <p class="text-sm font-bold text-slate-500 uppercase italic">Salida de Vehículos</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-white border border-outline-variant/30 flex items-center justify-center text-primary shadow-sm">
                    <span class="material-symbols-outlined">point_of_sale</span>
                </div>
            </header>

            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-10">
                    <h1 class="text-4xl font-black text-on-surface font-headline tracking-tight">Finalizar Estancia</h1>
                    <p class="text-slate-500 mt-2 italic font-medium">Calcula el tiempo y procesa el cobro de inmediato.</p>
                </div>

                <?php echo $mensaje; ?>

                <!-- BUSCADOR -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-outline-variant/20 mb-8">
                    <!-- ID añadido al form para envío automático -->
                    <form action="salida.php" method="POST" id="formBuscar" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Placa del Vehículo</label>
                            <input type="text" name="placa" id="inputPlaca" placeholder="A000000" required maxlength="8"
                                   class="w-full p-5 rounded-2xl border-2 border-slate-50 focus:border-primary outline-none uppercase font-mono text-2xl font-bold transition-all bg-slate-50 placeholder:text-slate-200"
                                   value="<?php echo htmlspecialchars($placa_prellenada); ?>">
                        </div>
                        <button type="submit" name="buscar_placa" id="btnBuscar" class="w-full md:w-auto bg-slate-900 text-white px-10 py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all shadow-xl flex items-center justify-center gap-2 group">
                            <span class="material-symbols-outlined group-hover:scale-110 transition-transform">search</span> BUSCAR
                        </button>
                    </form>
                </div>

                <?php if ($detalles_pago): ?>
                    <!-- TICKET DE COBRO GENERADO -->
                    <div id="card_detalles" class="bg-white rounded-[2.5rem] shadow-2xl border border-outline-variant/10 overflow-hidden animate-in zoom-in duration-300">
                        <div class="bg-primary p-8 text-white flex justify-between items-center">
                            <div>
                                <p class="text-blue-100 text-[10px] font-black uppercase tracking-widest mb-1">Ticket de Salida</p>
                                <h2 class="text-4xl font-black font-mono tracking-tighter"><?php echo strtoupper($detalles_pago['placa']); ?></h2>
                            </div>
                            <span class="material-symbols-outlined text-4xl opacity-50">receipt_long</span>
                        </div>
                        
                        <div class="p-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                                    <div class="flex items-center gap-3 mb-2 text-slate-400">
                                        <span class="material-symbols-outlined text-sm">schedule</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Hora de Entrada</span>
                                    </div>
                                    <p class="text-xl font-bold text-slate-700"><?php echo date('h:i A', strtotime($detalles_pago['entrada'])); ?></p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1"><?php echo date('d M, Y', strtotime($detalles_pago['entrada'])); ?></p>
                                </div>
                                <div class="p-6 bg-blue-50/50 rounded-3xl border border-blue-100">
                                    <div class="flex items-center gap-3 mb-2 text-primary">
                                        <span class="material-symbols-outlined text-sm">timer</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest">Tiempo/Tarifa</span>
                                    </div>
                                    <p class="text-xl font-bold text-primary"><?php echo $detalles_pago['tiempo']; ?></p>
                                    <p class="text-[10px] text-blue-400 font-bold uppercase mt-1 italic">Tarifa RD$ <?php echo number_format($precio_por_hora, 0); ?> / hr</p>
                                </div>
                            </div>
                            
                            <div class="flex flex-col md:flex-row justify-between items-center gap-8 pt-10 border-t border-dashed border-slate-200">
                                <div class="text-center md:text-left">
                                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2">Total Neto a Cobrar</p>
                                    <p class="text-6xl font-black text-slate-900 tracking-tight">
                                        <span class="text-2xl font-bold text-slate-300 mr-1">RD$</span><?php echo number_format($detalles_pago['total'], 0); ?>
                                    </p>
                                </div>
                                
                                <button onclick="abrirModalPago(<?php echo htmlspecialchars(json_encode($detalles_pago)); ?>)" 
                                        class="w-full md:w-auto bg-green-600 text-white px-12 py-6 rounded-3xl font-black hover:bg-green-700 transition-all shadow-2xl flex items-center justify-center gap-4 text-xs uppercase tracking-widest group">
                                    <span class="material-symbols-outlined text-2xl group-hover:scale-110">payments</span>
                                    Procesar Pago
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- MODALES DE PAGO (Efectivo/Tarjeta/Éxito) -->
    <div id="modalCobro" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 w-full max-w-md overflow-hidden relative">
            
            <!-- Pantalla 1: Detalles -->
            <div id="pantalla_detalles">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-headline font-black text-xl text-slate-800 italic">Resumen de Cuenta</h3>
                    <button onclick="cerrarModales()" class="text-slate-300 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
                </div>
                <div class="bg-slate-50 rounded-3xl p-6 mb-8 border border-slate-100">
                    <div class="flex justify-between mb-4 items-center">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Vehículo</span>
                        <span id="mod_placa" class="font-black text-slate-800 text-xl tracking-widest">---</span>
                    </div>
                    <div class="border-t border-dashed border-slate-200 pt-5 flex justify-between items-center">
                        <span class="text-[10px] font-black text-primary uppercase tracking-widest">Total</span>
                        <span class="text-4xl font-black text-slate-900 leading-none">RD$ <span id="mod_total">0</span></span>
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

            <!-- Pantalla 2: Confirmación Efectivo -->
            <div id="pantalla_confirmacion" class="hidden text-center py-4">
                <span class="material-symbols-outlined text-5xl text-orange-500 mb-4">help_center</span>
                <h3 class="font-black text-xl text-slate-800 mb-2">¿Confirmar Pago?</h3>
                <p class="text-slate-500 text-sm mb-8 px-6">Vas a registrar el cobro en efectivo por valor de <b class="text-slate-800">RD$ <span id="conf_monto">0</span></b></p>
                <div class="flex gap-3">
                    <button onclick="volverADetalles()" class="flex-1 py-4 rounded-2xl text-[10px] font-black uppercase text-slate-400 bg-slate-50 hover:bg-slate-100 transition-all">Cancelar</button>
                    <button onclick="confirmarPagoFinal(null, 'EFECTIVO')" class="flex-1 py-4 rounded-2xl text-[10px] font-black uppercase text-white bg-green-500 hover:bg-green-600 shadow-lg shadow-green-100 transition-all">Sí, Procesar</button>
                </div>
            </div>

            <!-- Pantalla 3: Pasarela Tarjeta -->
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
                    <button type="submit" id="btn_pagar_tarjeta" class="w-full bg-primary text-white py-5 rounded-[2rem] font-black uppercase text-[10px] tracking-widest shadow-xl mt-4 transition-all flex justify-center">Procesar Tarjeta</button>
                </form>
            </div>

            <!-- Pantalla 4: Éxito -->
            <div id="pantalla_exito" class="hidden text-center py-6">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6"><span class="material-symbols-outlined text-5xl">check_circle</span></div>
                <h3 class="font-black text-2xl text-slate-800 mb-2">¡Pago Exitoso!</h3>
                <p class="text-slate-500 text-sm mb-10 px-4">El vehículo ha sido liberado del sistema.</p>
                <a id="link_factura" href="#" target="_blank" class="block w-full bg-slate-900 text-white py-5 rounded-[2rem] font-black uppercase text-[10px] tracking-widest shadow-2xl flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined text-xl">print</span> Imprimir Recibo
                </a>
                <button onclick="window.location.href='salida.php'" class="mt-6 text-[10px] font-black text-slate-300 uppercase hover:text-primary tracking-[0.2em] transition-colors">Nueva Salida</button>
            </div>
        </div>
    </div>

    <script>
        let datosSalida = null;

        // Lógica de Modales
        function abrirModalPago(datos) {
            datosSalida = datos;
            document.getElementById('mod_placa').innerText = datos.placa.toUpperCase();
            document.getElementById('mod_total').innerText = datos.total.toLocaleString();
            document.getElementById('conf_monto').innerText = datos.total.toLocaleString();
            abrirPantalla('pantalla_detalles');
            document.getElementById('modalCobro').classList.remove('hidden');
        }

        function abrirPantalla(id) {
            ['pantalla_detalles', 'pantalla_confirmacion', 'pantalla_tarjeta', 'pantalla_exito'].forEach(p => {
                document.getElementById(p).classList.add('hidden');
            });
            document.getElementById(id).classList.remove('hidden');
        }

        function volverADetalles() { abrirPantalla('pantalla_detalles'); }
        function abrirConfirmacionEfectivo() { abrirPantalla('pantalla_confirmacion'); }
        function irAPasarela() { abrirPantalla('pantalla_tarjeta'); }
        function cerrarModales() { document.getElementById('modalCobro').classList.add('hidden'); }

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
                        id: datosSalida.id, 
                        metodo: metodo,
                        monto: datosSalida.total
                    })
                });

                const data = await response.json();
                if(data.status === 'success') {
                    abrirPantalla('pantalla_exito');
                    document.getElementById('link_factura').href = `ticket_salida.php?id=${datosSalida.id}`;
                    if(document.getElementById('card_detalles')) document.getElementById('card_detalles').classList.add('opacity-50', 'pointer-events-none');
                } else {
                    alert("Error: " + data.message);
                    if(metodo === 'TARJETA') {
                        btn.disabled = false;
                        btn.innerHTML = "Procesar Tarjeta";
                    }
                }
            } catch (e) {
                alert("Error de comunicación con el servidor.");
                if(metodo === 'TARJETA') {
                    btn.disabled = false;
                    btn.innerHTML = "Procesar Tarjeta";
                }
            }
        }

        // Lógica visual de la tarjeta
        document.getElementById('card_input')?.addEventListener('input', (e) => {
            let v = e.target.value.replace(/\D/g, '').match(/.{1,4}/g)?.join(' ') || '';
            e.target.value = v;
            document.getElementById('card_preview').innerText = v || "**** **** **** ****";
        });
        document.getElementById('expiry_input')?.addEventListener('input', (e) => {
            let v = e.target.value.replace(/\D/g, '');
            if(v.length > 2) v = v.substring(0,2) + '/' + v.substring(2,4);
            e.target.value = v;
            document.getElementById('expiry_preview').innerText = v || "00/00";
        });

        // Formateo de placa y AUTO-BÚSQUEDA
        window.onload = function() {
            const inputPlaca = document.getElementById('inputPlaca');
            const form = document.getElementById('formBuscar');
            const urlParams = new URLSearchParams(window.location.search);

            if (inputPlaca) {
                inputPlaca.addEventListener('input', function(e) {
                    e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                });
            }

            // SI LA PLACA VIENE POR URL (desde el Dashboard), BUSCAR AUTOMÁTICAMENTE
            if (urlParams.has('placa') && inputPlaca.value !== '') {
                // Añadimos un pequeño delay para que el usuario vea que se llenó
                setTimeout(() => {
                    const btnBuscar = document.getElementById('btnBuscar');
                    // Creamos un input hidden para simular el click del botón 'buscar_placa'
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'buscar_placa';
                    hiddenInput.value = '1';
                    form.appendChild(hiddenInput);
                    form.submit();
                }, 300);
            }
        };
    </script>
</body>
</html>