<?php
require_once 'functions.php';

// 1. Verificación de seguridad
if (!is_logged_in() || $_SESSION['user']['rol'] !== 'cliente') {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

// 2. Procesar el registro si se envía el formulario del modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_vehiculo'])) {
    $placa = strtoupper(trim($_POST['placa'])); // Forzamos mayúsculas para las placas
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);

    $stmt = $mysqli->prepare("INSERT INTO vehiculos (placa, marca, modelo, usuario_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('sssi', $placa, $marca, $modelo, $user_id);
    
    if ($stmt->execute()) {
        header("Location: mis_vehiculos.php?success=1");
        exit();
    }
}

// 3. Consulta de los vehículos actualizada
$res_vehiculos = $mysqli->query("SELECT * FROM vehiculos WHERE usuario_id = $user_id");
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Mis Vehículos - ParkControl</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Material+Symbols+Outlined:wght@200..700" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; }
        .fade-up { animation: fadeUp 0.4s ease-out forwards; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="flex bg-slate-50 min-h-screen">
    
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 ml-20 p-8 transition-all">
        <header class="mb-10">
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Gestión de Vehículos</h1>
            <p class="text-slate-500 text-sm">Administra tus unidades registradas en el sistema.</p>
        </header>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while($v = $res_vehiculos->fetch_assoc()): ?>
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-md transition-shadow fade-up">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-4">
                        <span class="material-symbols-outlined">directions_car</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight"><?php echo $v['placa']; ?></h3>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1"><?php echo $v['marca']; ?></p>
                    <p class="text-slate-600 text-sm font-medium"><?php echo $v['modelo']; ?></p>
                </div>
            <?php endwhile; ?>
            
            <!-- Botón para abrir el Modal -->
            <button onclick="toggleModal()" class="border-2 border-dashed border-slate-200 rounded-[2rem] p-8 flex flex-col items-center justify-center text-slate-400 hover:bg-white hover:border-blue-300 hover:text-blue-500 transition-all group fade-up">
                <span class="material-symbols-outlined text-4xl group-hover:scale-110 transition-transform">add_circle</span>
                <span class="text-xs font-black mt-3 uppercase tracking-tighter">Registrar Nuevo</span>
            </button>
        </div>
    </main>

    <!-- MODAL DE REGISTRO -->
    <div id="modalVehiculo" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-[2.5rem] w-full max-w-md p-10 shadow-2xl transform transition-all">
                
                <button onclick="toggleModal()" class="absolute top-6 right-6 text-slate-300 hover:text-slate-500 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>

                <header class="mb-8 text-center">
                    <h3 class="text-2xl font-black text-slate-900 italic">Nuevo Ingreso</h3>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Datos del Vehículo</p>
                </header>

                <form action="mis_vehiculos.php" method="POST" class="space-y-5">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 mb-2 block">Placa del Vehículo</label>
                        <input type="text" name="placa" required placeholder="ABC-123" 
                               class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 mb-2 block">Marca</label>
                            <input type="text" name="marca" required placeholder="Toyota" 
                                   class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1 mb-2 block">Modelo</label>
                            <input type="text" name="modelo" required placeholder="Camry" 
                                   class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                        </div>
                    </div>

                    <button type="submit" name="registrar_vehiculo" 
                            class="w-full bg-slate-900 hover:bg-blue-600 text-white font-black uppercase tracking-[0.2em] py-5 rounded-2xl shadow-xl transition-all text-[10px] mt-4">
                        Confirmar Registro
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Máscara para placas de RD (Ej: A123456)
    const inputPlaca = document.querySelector('input[name="placa"]');
    
    inputPlaca.addEventListener('input', function (e) {
        let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); // Solo letras y números
        
        // Si el primer carácter es una letra, limitamos a 1 letra y hasta 6 números
        if (value.length > 0) {
            const firstChar = value.charAt(0);
            if (/[A-Z]/.test(firstChar)) {
                const numbers = value.substring(1, 7).replace(/[^0-9]/g, '');
                value = firstChar + numbers;
            } else {
                // Si empieza con número (placas antiguas o especiales), limitamos a 7 caracteres
                value = value.substring(0, 7);
            }
        }
        
        e.target.value = value;
    });

    function toggleModal() {
        const modal = document.getElementById('modalVehiculo');
        modal.classList.toggle('hidden');
    }
</script>
</body>
</html>