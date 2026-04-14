<?php
require_once 'functions.php';
require_login();
$user = $_SESSION['user'];
$inicial = strtoupper(substr($user['nombre'], 0, 1));
?>
<!doctype html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8" />
    <title>ParkControl - Mi Perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#005ac1' },
                    fontFamily: { headline: ['Manrope', 'sans-serif'], sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-[#f8f9ff] min-h-screen">
    
    <?php include 'sidebar.php'; ?>

    <main class="ml-20 p-8 min-h-screen flex flex-col items-center justify-center transition-all duration-300">
        
        <div class="max-w-md w-full mb-6 text-left">
            <h1 class="font-headline font-black text-3xl text-slate-800">Mi Perfil</h1>
            <p class="text-slate-500 font-medium text-sm">Gestiona tu identidad en el sistema</p>
        </div>

        <form action="update_profile.php" method="POST" class="max-w-md w-full bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/5 border border-slate-100 overflow-hidden">
            
            <div class="bg-primary h-32 flex items-end justify-center relative">
                <div class="relative translate-y-14">
                    <div class="w-28 h-28 bg-white rounded-full border-[6px] border-white flex items-center justify-center text-primary text-4xl font-black shadow-lg">
                        <?php echo $inicial; ?>
                    </div>
                </div>
            </div>

            <div class="pt-20 pb-10 px-10">
                <div class="space-y-5">
                    
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-1 block">Nombre Completo</label>
                        <input type="text" name="nombre" value="<?php echo $user['nombre']; ?>" required
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-1 block">Nombre de Usuario</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">@</span>
                            <input type="text" name="username" value="<?php echo $user['username']; ?>" required
                                   class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-10 pr-5 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">ID</p>
                            <p class="font-black text-slate-700">#00<?php echo $user['id']; ?></p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Estado</p>
                            <p class="font-black text-green-600 uppercase text-[10px]">Activo</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full mt-8 bg-primary text-white font-black text-xs uppercase tracking-[0.2em] py-4 rounded-2xl shadow-lg shadow-primary/30 hover:scale-[1.02] active:scale-95 transition-all">
                    Actualizar Datos
                </button>
            </div>
        </form>
    </main>
</body>
</html>