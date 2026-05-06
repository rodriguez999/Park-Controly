<?php
require_once 'functions.php';
// Solo el admin puede entrar aquí
require_admin();

// Datos del usuario logueado
$user_id = $_SESSION['user']['id'];
$user_name = $_SESSION['user']['nombre'];

$mensaje = '';

// Lógica para actualizar la configuración
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_tarifa = floatval($_POST['tarifa_hora']);
    $nueva_capacidad = intval($_POST['capacidad_total']);

    $stmt = $mysqli->prepare("UPDATE configuracion SET tarifa_hora = ?, capacidad_total = ? WHERE id = 1");
    $stmt->bind_param('di', $nueva_tarifa, $nueva_capacidad);

    if ($stmt->execute()) {
        $mensaje = '
        <div class="mb-8 p-5 rounded-[2.5rem] bg-green-50 text-green-700 border border-green-100 flex items-center gap-4 animate-in fade-in slide-in-from-top duration-500 shadow-sm">
            <div class="w-12 h-12 bg-green-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-green-200">
                <span class="material-symbols-outlined text-2xl">done_all</span>
            </div>
            <div>
                <p class="font-black text-sm uppercase tracking-wider leading-none mb-1">Configuración Actualizada</p>
                <p class="text-xs opacity-80 font-medium text-green-600/80">Los nuevos parámetros operativos ya están en vigor.</p>
            </div>
        </div>';
    } else {
        $mensaje = '
        <div class="mb-8 p-5 rounded-[2.5rem] bg-red-50 text-red-700 border border-red-100 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 bg-red-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-red-200">
                <span class="material-symbols-outlined text-2xl">warning</span>
            </div>
            <p class="font-bold text-sm">Error al guardar: ' . $mysqli->error . '</p>
        </div>';
    }
}

// Obtener los valores actuales de la base de datos
$config = get_config($mysqli);
?>
<!doctype html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ParkControl - Configuración General</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: { 
                'surface-dim': '#f1f3f9', 
                'primary': '#005ac1', 
                'on-surface': '#1a1c1e', 
                'outline-variant': '#c3c7cf' 
            }
          }
        }
      }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .font-headline { font-family: 'Manrope', sans-serif; }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        main { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-surface-dim min-h-screen">

    <div class="flex">
        <?php include 'sidebar.php'; ?>

        <!-- Se aumentó el padding y se centró el contenido máximo para mejor lectura -->
        <main class="flex-1 pl-28 pr-8 lg:pr-16 py-12 transition-all duration-300">
            
            <header class="mb-14">
                <h2 class="text-[10px] font-black text-primary uppercase tracking-[0.4em] mb-2 leading-none">Sistema de Gestión</h2>
                <h1 class="font-headline text-5xl font-black text-on-surface tracking-tight">Ajustes Operativos</h1>
                <p class="text-slate-400 mt-2 font-medium">Modifica los valores base que rigen el funcionamiento del parqueo.</p>
            </header>

            <div class="max-w-6xl">
                <?php echo $mensaje; ?>

                <div class="grid lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Lado Izquierdo: Información y Estado (4 columnas) -->
                    <div class="lg:col-span-4 space-y-6">
                        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm relative overflow-hidden group">
                            <div class="relative z-10">
                                <div class="w-12 h-12 bg-blue-50 text-primary rounded-2xl flex items-center justify-center mb-6">
                                    <span class="material-symbols-outlined text-2xl font-bold">payments</span>
                                </div>
                                <h3 class="font-headline font-black text-lg mb-2 text-slate-800">Política de Cobro</h3>
                                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                                    El sistema aplica la regla de <span class="text-primary font-bold">fracción de hora como hora completa</span>. Los cambios realizados aquí afectarán a todos los vehículos que marquen salida desde este momento.
                                </p>
                            </div>
                        </div>

                        <div class="bg-slate-900 text-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-ping"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Terminal Sincronizada</span>
                            </div>
                            <p class="text-xs font-medium text-slate-400 mb-1">Última actualización de parámetros:</p>
                            <p class="font-mono text-sm font-bold text-blue-300"><?php echo date('d M, Y - h:i A'); ?></p>
                        </div>
                    </div>

                    <!-- Lado Derecho: Formulario (8 columnas) -->
                    <div class="lg:col-span-8">
                        <div class="bg-white rounded-[3.5rem] shadow-sm border border-slate-200 p-8 lg:p-12">
                            <form action="configuracion.php" method="POST" class="grid md:grid-cols-2 gap-10">
                                
                                <!-- Tarifa -->
                                <div class="space-y-4">
                                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Tarifa por Hora (RD$)</label>
                                    <div class="relative group">
                                        <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 font-black text-2xl group-focus-within:text-primary transition-colors">$</div>
                                        <input type="number" step="0.01" name="tarifa_hora" 
                                               value="<?php echo $config['tarifa_hora']; ?>"
                                               class="w-full pl-14 pr-6 py-8 bg-slate-50 border-2 border-slate-50 rounded-[2rem] focus:border-primary focus:bg-white outline-none transition-all font-black text-4xl text-slate-800 group-hover:border-slate-200" 
                                               required>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold italic ml-2">* Se recomienda múltiplos de 25 o 50.</p>
                                </div>

                                <!-- Capacidad -->
                                <div class="space-y-4">
                                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Capacidad Máxima</label>
                                    <div class="relative group">
                                        <span class="material-symbols-outlined absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-all text-2xl">directions_car</span>
                                        <input type="number" name="capacidad_total" 
                                               value="<?php echo $config['capacidad_total']; ?>"
                                               class="w-full pl-16 pr-6 py-8 bg-slate-50 border-2 border-slate-50 rounded-[2rem] focus:border-primary focus:bg-white outline-none transition-all font-black text-4xl text-slate-800 group-hover:border-slate-200" 
                                               required>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold italic ml-2">* Espacios totales disponibles en el local.</p>
                                </div>

                                <!-- Botón de Guardar que ocupa las 2 columnas -->
                                <div class="md:col-span-2 pt-4">
                                    <button type="submit" class="w-full bg-primary text-white font-black py-8 rounded-[2.5rem] hover:bg-slate-900 transition-all shadow-2xl shadow-blue-100 flex items-center justify-center gap-4 text-xs tracking-[0.3em] uppercase active:scale-[0.98] group">
                                        <span class="material-symbols-outlined group-hover:rotate-180 transition-transform duration-700">settings_backup_restore</span>
                                        Actualizar Parámetros Operativos
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        </main>
    </div>

    <script>
        // Lógica de navegación activa simplificada
        const currentPath = window.location.pathname.split('/').pop() || 'configuracion.php';
        document.querySelectorAll('aside nav a').forEach(link => {
            if(link.getAttribute('href') === currentPath) {
                link.classList.add('bg-primary/10', 'text-primary', 'font-bold');
                link.classList.remove('text-gray-500');
            }
        });
    </script>
</body>
</html>