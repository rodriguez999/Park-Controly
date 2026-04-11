<?php
require_once 'functions.php';
require_login();

$mensaje = "";
$detalles_pago = null;

// 1. BUSCAR VEHÍCULO PARA SALIDA
if (isset($_POST['buscar_placa'])) {
    $placa = $_POST['placa'];
    $res = $mysqli->query("SELECT * FROM movimientos WHERE placa = '$placa' AND estado = 'EN_PARQUEO' LIMIT 1");
    
    if ($res->num_rows > 0) {
        $movimiento = $res->fetch_assoc();
        $entrada = new DateTime($movimiento['hora_entrada']);
        $ahora = new DateTime();
        $diferencia = $entrada->diff($ahora);
        
        // Cálculo de horas (mínimo 1 hora)
        $horas = $diferencia->h + ($diferencia->days * 24);
        if ($diferencia->i > 0) $horas++; 
        if ($horas == 0) $horas = 1;

        $precio_por_hora = 75; // Esto podrías traerlo de tu tabla 'tarifas_tipo'
        $total = $horas * $precio_por_hora;

        $detalles_pago = [
            'id' => $movimiento['id'],
            'placa' => $placa,
            'entrada' => $movimiento['hora_entrada'],
            'tiempo' => $horas . " hora(s)",
            'total' => $total
        ];
    } else {
        $mensaje = "No se encontró un vehículo con esa placa en el parqueo.";
    }
}

// 2. CONFIRMAR SALIDA Y COBRO
if (isset($_POST['confirmar_pago'])) {
    $id = $_POST['id_movimiento'];
    $monto = $_POST['monto'];
    
    $stmt = $mysqli->prepare("UPDATE movimientos SET hora_salida = NOW(), total_pago = ?, estado = 'COMPLETADO' WHERE id = ?");
    $stmt->bind_param("di", $monto, $id);
    
    if ($stmt->execute()) {
        $mensaje = "Salida registrada con éxito. ¡Cobro completado!";
    } else {
        $mensaje = "Error al procesar el pago.";
    }
}
?>
<!doctype html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ParkControl - Registrar Salida</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: { 'surface-dim': '#f1f3f9', 'primary': '#005ac1', 'on-surface': '#1a1c1e' }
          }
        }
      }
    </script>
</head>
<body class="bg-surface-dim min-h-screen">
    <div class="flex">
        <aside class="w-20 lg:w-64 bg-white min-h-screen border-r border-gray-200 flex flex-col">
            <div class="p-6 flex items-center gap-3">
                <div class="bg-primary w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold">P</div>
                <a class="font-bold text-xl hidden lg:block text-primary" href="menu.php">ParkControl</a>
            </div>
            <nav class="flex-1 mt-4 px-3 space-y-2">
                <a href="menu.php" class="flex items-center gap-4 p-3 text-gray-600 hover:bg-gray-100 rounded-xl">
                    <span class="material-symbols-outlined">dashboard</span><span class="hidden lg:block">Dashboard</span>
                </a>
                <a href="entrada.php" class="flex items-center gap-4 p-3 text-on-surface-variant hover:bg-gray-100 rounded-xl transition-all">
                    <span class="material-symbols-outlined">login</span>
                    <span class="hidden lg:block">Registrar Entrada</span>
                </a>
                <a href="salida.php" class="flex items-center gap-4 p-3 bg-primary/10 text-primary rounded-xl font-semibold">
                    <span class="material-symbols-outlined">logout</span><span class="hidden lg:block">Registrar Salida</span>
                </a>
                <a href="logout.php" class="flex items-center gap-4 p-3 text-red-600 hover:bg-red-50 rounded-xl transition-all mt-10">
                    <span class="material-symbols-outlined">power_settings_new</span>
                    <span class="hidden lg:block">Cerrar Sesión</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <div class="max-w-3xl mx-auto">
                <header class="mb-8">
                    <h1 class="text-3xl font-bold text-on-surface font-headline italic">Finalizar Estancia</h1>
                    <p class="text-gray-500">Calcula el tiempo de parqueo y procesa el pago.</p>
                </header>

                <?php if ($mensaje): ?>
                    <div class="mb-6 p-4 rounded-2xl bg-blue-100 text-blue-700 font-bold border border-blue-200">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 mb-8">
                    <form action="salida.php" method="POST" class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" name="placa" placeholder="BUSCAR PLACA (Ej: ABC-123)" required
                                   class="w-full p-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary outline-none uppercase font-mono">
                        </div>
                        <button type="submit" name="buscar_placa" class="bg-primary text-white px-8 rounded-xl font-bold hover:bg-blue-700 transition-all">
                            Buscar
                        </button>
                    </form>
                </div>

                <?php if ($detalles_pago): ?>
                    <div class="bg-white rounded-3xl shadow-xl border-2 border-primary/20 overflow-hidden animate-in fade-in zoom-in duration-300">
                        <div class="bg-primary p-6 text-white">
                            <h2 class="text-xl font-bold">Ticket de Salida: <?php echo $detalles_pago['placa']; ?></h2>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-2 gap-8 mb-8">
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase">Hora de Entrada</p>
                                    <p class="text-lg font-semibold"><?php echo $detalles_pago['entrada']; ?></p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase">Tiempo Transcurrido</p>
                                    <p class="text-lg font-semibold text-primary"><?php echo $detalles_pago['tiempo']; ?></p>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-6 rounded-2xl flex justify-between items-center border border-gray-100">
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Total a Pagar</p>
                                    <p class="text-4xl font-black text-on-surface">$<?php echo number_format($detalles_pago['total'], 2); ?></p>
                                </div>
                                <form action="salida.php" method="POST">
                                    <input type="hidden" name="id_movimiento" value="<?php echo $detalles_pago['id']; ?>">
                                    <input type="hidden" name="monto" value="<?php echo $detalles_pago['total']; ?>">
                                    <button type="submit" name="confirmar_pago" class="bg-green-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-green-700 transition-all shadow-lg shadow-green-100">
                                        Procesar Pago
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>