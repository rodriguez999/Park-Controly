<?php
require_once 'functions.php';
require_login();

// Obtenemos datos del usuario logueado
$user_id = $_SESSION['user']['id'];
$user_name = $_SESSION['user']['nombre'];
$user_rol = $_SESSION['user']['rol'] ?? 'usuario';

// Obtener la configuración dinámica de la base de datos
$config = get_config($mysqli);
$tarifa_actual = $config['tarifa_hora'];

$mensaje = '';

// Lógica de registro de entrada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $placa = strtoupper(trim($_POST['placa']));
    
    // Validar si el vehículo ya está dentro
    $check_stmt = $mysqli->prepare("SELECT id FROM movimientos WHERE placa = ? AND estado = 'EN_PARQUEO'");
    $check_stmt->bind_param('s', $placa);
    $check_stmt->execute();
    $check_res = $check_stmt->get_result();
    
    if ($check_res->num_rows > 0) {
        $mensaje = '
        <div class="mb-6 p-4 rounded-2xl bg-orange-100 text-orange-700 font-bold border border-orange-200 flex items-center gap-3 animate-pulse">
            <span class="material-symbols-outlined">warning</span>
            Este vehículo ya tiene una entrada activa en el parqueo.
        </div>';
    } else {
        // Registro de la entrada
        $stmt = $mysqli->prepare(
            "INSERT INTO movimientos (placa, hora_entrada, estado) VALUES (?, NOW(), 'EN_PARQUEO')"
        );
        $stmt->bind_param('s', $placa);

        if ($stmt->execute()) {
            $nuevo_id = $mysqli->insert_id;
            $mensaje = '
            <div class="mb-6 p-6 rounded-3xl bg-green-100 text-green-800 border border-green-200 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <span class="material-symbols-outlined text-green-600 text-3xl">check_circle</span>
                    <span class="font-bold text-xl">¡Entrada Registrada!</span>
                </div>
                <p class="mb-4">Vehículo con placa <span class="font-mono font-bold text-lg">'.$placa.'</span> listo para parquear.</p>
                <a href="ticket.php?id=' . $nuevo_id . '" target="_blank" 
                   class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-green-700 transition-all shadow-md">
                    <span class="material-symbols-outlined">print</span>
                    Imprimir Ticket de Entrada
                </a>
            </div>';
        } else {
            $mensaje = '<div class="mb-6 p-4 rounded-2xl bg-red-100 text-red-700 font-bold border border-red-200">Error: ' . $mysqli->error . '</div>';
        }
    }
}
?>
<!doctype html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ParkControl - Registro de Entrada</title>
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
              'on-surface-variant': '#43474e',
              'outline-variant': '#c3c7cf',
            }
          }
        }
      }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .font-headline { font-family: 'Manrope', sans-serif; }
        .sidebar-text { white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity 0.2s ease-in-out, visibility 0.2s; }
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
                    <h2 class="text-[10px] font-black text-primary uppercase tracking-[0.2em] leading-none mb-1">Operaciones</h2>
                    <p class="text-sm font-bold text-slate-500 uppercase tracking-tighter italic">Registro de Ingresos</p>
                </div>

                <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-full border border-outline-variant/30 shadow-sm">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Sistema en línea</p>
                </div>
            </header>
            
            <div class="max-w-2xl mx-auto">
                <div class="text-center mb-10">
                    <h1 class="text-4xl font-black text-on-surface font-headline mb-3 tracking-tight">Registro de Entrada</h1>
                    <p class="text-on-surface-variant">Ingresa la placa del vehículo para iniciar el tiempo</p>
                </div>

                <?php echo $mensaje; ?>

                <div class="bg-white p-8 lg:p-10 rounded-[2.5rem] shadow-sm border border-outline-variant/20">
                    <form action="entrada.php" method="POST" class="space-y-8">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Número de Placa (RD)</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-3xl">directions_car</span>
                                <input type="text" name="placa" id="inputPlaca" placeholder="A000000" required maxlength="8"
                                       class="w-full pl-16 pr-6 py-6 rounded-3xl border-2 border-slate-100 focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all uppercase text-3xl font-mono font-bold tracking-[0.15em] placeholder:text-slate-100">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Tarifa Aplicable</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-primary">payments</span>
                                <div class="w-full pl-14 pr-8 py-5 rounded-2xl border-2 border-primary/20 bg-primary/5 font-bold text-primary flex justify-between items-center">
                                    <span class="text-xs uppercase tracking-tight">Tarifa por Hora</span>
                                    <span class="text-xl">RD$ <?php echo number_format($tarifa_actual, 2); ?></span>
                                </div>
                                <input type="hidden" name="tipo_tarifa" value="1">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-primary text-white font-black text-xs uppercase tracking-[0.2em] py-6 rounded-3xl hover:bg-blue-700 transition-all flex items-center justify-center gap-4 shadow-2xl shadow-blue-200 group active:scale-[0.98]">
                            <span class="material-symbols-outlined group-hover:rotate-12 transition-transform">confirmation_number</span>
                            Confirmar e Imprimir
                        </button>
                    </form>
                </div>

                <div class="mt-8 flex gap-4 p-6 bg-blue-50/50 rounded-3xl border border-blue-100 items-start">
                    <span class="material-symbols-outlined text-primary">info</span>
                    <div>
                        <p class="text-xs text-on-surface-variant leading-relaxed">
                            Se aplicará el redondeo por hora iniciada basado en la tarifa de 
                            <span class="font-bold text-primary">RD$ <?php echo number_format($tarifa_actual, 2); ?></span> configurada por el administrador.
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const inputPlaca = document.getElementById('inputPlaca');
        inputPlaca.addEventListener('input', function(e) {
            let val = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            e.target.value = val;
        });

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