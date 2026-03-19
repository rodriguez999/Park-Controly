<?php
require_once 'functions.php';
require_login();

$mensaje = "";

// Lógica de registro de entrada
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $placa = $_POST['placa'];
    $tipo_tarifa = $_POST['tipo_tarifa'];
    
    $stmt = $mysqli->prepare("INSERT INTO movimientos (placa, hora_entrada, estado) VALUES (?, NOW(), 'EN_PARQUEO')");
    $stmt->bind_param("s", $placa);
    
    if ($stmt->execute()) {
        $mensaje = "Entrada registrada con éxito para la placa: " . $placa;
    } else {
        $mensaje = "Error al registrar: " . $mysqli->error;
    }
}
?>
<!doctype html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>ParkControl - Registrar Entrada</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
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
</head>
<body class="bg-surface-dim min-h-screen">
    <div class="flex">
        <aside class="w-20 lg:w-64 bg-white min-h-screen border-r border-outline-variant/30 flex flex-col">
            <div class="p-6 flex items-center gap-3">
                <div class="bg-primary w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold">P</div>
                <span class="font-bold text-xl hidden lg:block">ParkControl</span>
            </div>
            <nav class="flex-1 mt-4 px-3 space-y-2">
                <a href="menu.php" class="flex items-center gap-4 p-3 text-on-surface-variant hover:bg-gray-100 rounded-xl">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span class="hidden lg:block">Dashboard</span>
                </a>
                <a href="entrada.php" class="flex items-center gap-4 p-3 bg-primary/10 text-primary rounded-xl font-semibold">
                    <span class="material-symbols-outlined">login</span>
                    <span class="hidden lg:block">Registrar Entrada</span>
                </a>
                <a href="salida.php" class="flex items-center gap-4 p-3 text-on-surface-variant hover:bg-gray-100 rounded-xl">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="hidden lg:block">Registrar Salida</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <div class="max-w-2xl mx-auto">
                <header class="mb-8 text-center">
                    <h1 class="text-3xl font-bold text-on-surface font-headline">Registro de Entrada</h1>
                    <p class="text-on-surface-variant">Ingresa la placa del vehículo para iniciar la sesión</p>
                </header>

                <?php if ($mensaje): ?>
                    <div class="mb-6 p-4 rounded-2xl bg-green-100 text-green-700 font-bold border border-green-200">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-outline-variant/20">
                    <form action="entrada.php" method="POST" class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-on-surface mb-2">Número de Placa</label>
                            <input type="text" name="placa" placeholder="EJ: A123456" required
                                   class="w-full p-4 rounded-xl border border-outline-variant focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all uppercase text-lg font-mono tracking-widest">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-on-surface mb-2">Tipo de Tarifa</label>
                            <select name="tipo_tarifa" class="w-full p-4 rounded-xl border border-outline-variant focus:ring-2 focus:ring-primary outline-none bg-white">
                                <option value="1">Por Hora (Regular)</option>
                                <option value="2">Día Completo</option>
                                <option value="3">Membresía Mensual</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-primary text-white font-bold py-4 rounded-xl hover:bg-blue-700 transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                            <span class="material-symbols-outlined">check_circle</span>
                            Confirmar Entrada
                        </button>
                    </form>
                </div>

                <div class="mt-8 flex gap-4 p-4 bg-blue-50 rounded-2xl border border-blue-100">
                    <span class="material-symbols-outlined text-primary">info</span>
                    <p class="text-sm text-blue-800 italic">Recuerda que el sistema registrará la hora exacta de entrada automáticamente.</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>