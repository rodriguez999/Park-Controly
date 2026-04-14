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
        body { font-family: 'Inter', sans-serif; }
        .font-headline { font-family: 'Manrope', sans-serif; }
        
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        
        .card-blur { backdrop-filter: blur(10px); background-color: rgba(255, 255, 255, 0.8); }
    </style>
</head>
<body class="bg-surface-dim min-h-screen">

    <div class="flex">
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 pl-24 pr-4 lg:pr-10 py-10 transition-all duration-300">
            <header class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-[10px] font-black text-primary uppercase tracking-[0.3em] mb-1 leading-none">Panel de Control</h2>
                    <h1 class="font-headline text-4xl font-black text-on-surface tracking-tight">Ajustes Generales</h1>
                </div>
                
                <div class="relative group">
                    <button id="userMenuBtn" class="flex items-center gap-3 bg-white p-1.5 pr-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all active:scale-95">
                        <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-black shadow-lg shadow-blue-100">
                            <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                        </div>
                        <div class="text-left hidden md:block">
                            <p class="text-xs font-black text-slate-800 leading-none mb-0.5"><?php echo $user_name; ?></p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Administrador</p>
                        </div>
                        <span class="material-symbols-outlined text-slate-300 text-sm ml-2 group-hover:text-primary transition-colors">expand_more</span>
                    </button>

                    <div id="userMenu" class="hidden absolute right-0 top-16 w-52 bg-white shadow-2xl rounded-3xl border border-slate-100 z-50 overflow-hidden py-2 animate-in fade-in zoom-in-95 duration-200">
                        <a href="perfil.php" class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50 text-slate-600 font-bold text-sm transition-colors">
                            <span class="material-symbols-outlined text-xl opacity-50">account_circle</span> Perfil
                        </a>
                        <hr class="my-2 border-slate-100">
                        <a href="logout.php" class="flex items-center gap-3 px-5 py-3.5 hover:bg-red-50 text-red-600 font-bold text-sm transition-colors">
                            <span class="material-symbols-outlined text-xl">power_settings_new</span> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </header>

            <div class="max-w-5xl">
                <?php echo $mensaje; ?>

                <div class="grid lg:grid-cols-5 gap-10">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-primary text-white p-8 rounded-[2.5rem] shadow-xl shadow-blue-200 relative overflow-hidden group">
                            <div class="relative z-10">
                                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mb-6 backdrop-blur-md">
                                    <span class="material-symbols-outlined text-3xl">info</span>
                                </div>
                                <h3 class="font-headline font-black text-xl mb-3">Parámetros de Cálculo</h3>
                                <p class="text-blue-50 text-sm leading-relaxed font-medium opacity-90">
                                    El sistema utiliza una política de <span class="text-white font-bold underline decoration-blue-300 underline-offset-4">hora iniciada, hora cobrada</span>. 
                                    Asegúrese de que la tarifa sea acorde a la moneda local (RD$).
                                </p>
                            </div>
                            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all"></div>
                        </div>
                        
                        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm flex items-center gap-5">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                                <span class="material-symbols-outlined text-2xl">update</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Estado de Sincronización</p>
                                <p class="font-bold text-slate-700 text-sm italic">Actualizado: <?php echo date('d/m/Y - H:i'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-3">
                        <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-8 lg:p-14">
                                <form action="configuracion.php" method="POST" class="space-y-12">
                                    
                                    <div class="group">
                                        <div class="flex justify-between items-end mb-4 px-2">
                                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Tarifa por Hora</label>
                                            <span class="text-[10px] font-bold text-primary bg-primary/5 px-3 py-1 rounded-full uppercase">Moneda: RD$</span>
                                        </div>
                                        <div class="relative">
                                            <div class="absolute left-7 top-1/2 -translate-y-1/2 text-slate-300 font-black text-2xl group-focus-within:text-primary transition-colors">$</div>
                                            <input type="number" step="0.01" name="tarifa_hora" 
                                                   value="<?php echo $config['tarifa_hora']; ?>"
                                                   class="w-full pl-16 pr-8 py-7 bg-slate-50 border-2 border-slate-50 rounded-[2rem] focus:border-primary focus:bg-white outline-none transition-all font-black text-3xl text-slate-700 shadow-inner group-hover:border-slate-200" 
                                                   required>
                                        </div>
                                    </div>

                                    <div class="group">
                                        <div class="flex justify-between items-end mb-4 px-2">
                                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Aforo Máximo</label>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter italic italic">Límite físico de la terminal</span>
                                        </div>
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-7 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary transition-all text-2xl">garage</span>
                                            <input type="number" name="capacidad_total" 
                                                   value="<?php echo $config['capacidad_total']; ?>"
                                                   class="w-full pl-16 pr-8 py-7 bg-slate-50 border-2 border-slate-50 rounded-[2rem] focus:border-primary focus:bg-white outline-none transition-all font-black text-3xl text-slate-700 shadow-inner group-hover:border-slate-200" 
                                                   required>
                                        </div>
                                    </div>

                                    <div class="pt-6">
                                        <button type="submit" class="w-full bg-slate-900 text-white font-black py-7 rounded-[2rem] hover:bg-primary transition-all shadow-2xl shadow-slate-200 flex items-center justify-center gap-4 text-xs tracking-[0.3em] uppercase active:scale-[0.97] group">
                                            <span class="material-symbols-outlined group-hover:rotate-180 transition-transform duration-500">sync</span>
                                            Sincronizar Parámetros
                                        </button>
                                        <p class="text-center text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-6 opacity-60">Acción protegida por credenciales de administrador</p>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Dropdown usuario
        const menuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');

        menuBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', () => userMenu?.classList.add('hidden'));

        // Navegación activa
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