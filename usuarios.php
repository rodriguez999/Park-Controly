<?php
require_once 'functions.php';
// Protección de nivel de administrador
require_admin(); 

$mensaje = '';

// --- LÓGICA DE REGISTRO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    $nombre = trim($_POST['nombre']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    // Verificar si el usuario ya existe
    $check = $mysqli->prepare("SELECT id FROM usuarios WHERE username = ?");
    $check->bind_param('s', $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $mensaje = '<div class="bg-amber-50 text-amber-700 p-5 rounded-3xl mb-8 flex items-center gap-4 border border-amber-100 italic font-medium shadow-sm">
                        <span class="material-symbols-outlined">warning</span>
                        <p class="text-sm">El identificador @'.$username.' ya está en uso. Intenta con otro.</p>
                    </div>';
    } else {
        $stmt = $mysqli->prepare("INSERT INTO usuarios (nombre, username, password, rol) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $nombre, $username, $password, $rol);
        if ($stmt->execute()) {
            $mensaje = '
            <div class="mb-8 p-5 rounded-[2.5rem] bg-green-50 text-green-700 border border-green-100 flex items-center gap-4 animate-in fade-in slide-in-from-top duration-500 shadow-sm">
                <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-green-100">
                    <span class="material-symbols-outlined text-xl font-bold">person_add</span>
                </div>
                <div>
                    <p class="font-black text-sm uppercase tracking-tight">Usuario Registrado</p>
                    <p class="text-xs opacity-80 font-medium">El nuevo miembro ha sido añadido al sistema con éxito.</p>
                </div>
            </div>';
        }
    }
}

// --- LÓGICA DE ELIMINACIÓN ---
if (isset($_GET['eliminar'])) {
    $id_a_borrar = intval($_GET['eliminar']);
    if ($id_a_borrar === $_SESSION['user']['id']) {
        $mensaje = '<div class="bg-red-50 text-red-700 p-5 rounded-3xl mb-8 flex items-center gap-4 border border-red-100 animate-pulse">
                        <span class="material-symbols-outlined">error</span>
                        <p class="font-bold text-sm">Acción protegida: No puedes eliminar tu propia cuenta.</p>
                    </div>';
    } else {
        $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param('i', $id_a_borrar);
        if ($stmt->execute()) {
            $mensaje = '<div class="bg-slate-900 text-white p-5 rounded-3xl mb-8 flex items-center gap-4 shadow-2xl">
                            <span class="material-symbols-outlined text-red-400">delete_sweep</span>
                            <p class="font-bold text-sm">Usuario removido del sistema correctamente.</p>
                        </div>';
        }
    }
}

// Obtener lista de usuarios actualizada
$res_usuarios = $mysqli->query("SELECT id, username, nombre, rol FROM usuarios ORDER BY rol ASC, nombre ASC");
?>
<!doctype html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ParkControl - Gestión de Usuarios</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#005ac1', 'surface-dim': '#f1f3f9' }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-headline { font-family: 'Manrope', sans-serif; }
        .modal-overlay { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="bg-surface-dim min-h-screen">
    
    <div class="flex">
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 pl-24 pr-4 lg:pr-10 py-10 transition-all duration-300">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                <div>
                    <h2 class="text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-1 leading-none">Administración</h2>
                    <h1 class="font-headline text-4xl font-black text-slate-900 tracking-tight">Usuarios</h1>
                </div>
                
                <button onclick="toggleModal('modalRegistro')" class="bg-primary text-white px-8 py-4 rounded-2xl text-sm font-bold hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 flex items-center justify-center gap-3 group">
                    <span class="material-symbols-outlined text-xl group-hover:scale-110 transition-transform">add_circle</span> 
                    Registrar Nuevo
                </button>
            </header>

            <?php echo $mensaje; ?>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-slate-400 text-[10px] uppercase font-black tracking-[0.2em] bg-slate-50/50">
                                <th class="px-8 py-6">Perfil</th>
                                <th class="px-8 py-6">Identificador</th>
                                <th class="px-8 py-6 text-center">Nivel</th>
                                <th class="px-8 py-6 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php while($u = $res_usuarios->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-black text-sm group-hover:bg-primary group-hover:text-white transition-all shadow-inner">
                                            <?php echo strtoupper(substr($u['nombre'], 0, 1)); ?>
                                        </div>
                                        <span class="font-bold text-slate-800 text-sm tracking-tight"><?php echo $u['nombre']; ?></span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-sm italic text-slate-400">@<?php echo $u['username']; ?></td>
                                <td class="px-8 py-6 text-center">
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider <?php echo ($u['rol'] == 'admin') ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-blue-50 text-blue-600 border border-blue-100'; ?>">
                                        <?php echo $u['rol']; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <?php if($u['id'] !== $_SESSION['user']['id']): ?>
                                        <a href="usuarios.php?eliminar=<?php echo $u['id']; ?>" 
                                           onclick="return confirm('¿Confirmar eliminación permanente de este usuario?')"
                                           class="w-10 h-10 inline-flex items-center justify-center rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all">
                                            <span class="material-symbols-outlined text-xl">delete</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-[9px] font-black text-primary/40 uppercase tracking-widest bg-primary/5 px-3 py-2 rounded-lg italic">Tú</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="modalRegistro" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 modal-overlay">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="font-headline font-black text-xl text-slate-900 leading-none mb-1">Nuevo Usuario</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Credenciales de acceso</p>
                </div>
                <button onclick="toggleModal('modalRegistro')" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-white hover:shadow-sm text-slate-400 transition-all">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form action="usuarios.php" method="POST" class="p-8 space-y-6">
                <input type="hidden" name="accion" value="registrar">
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Nombre Completo</label>
                    <input type="text" name="nombre" placeholder="Ej: Juan Perez" required 
                           class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl focus:bg-white focus:border-primary outline-none font-bold text-slate-700 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Username</label>
                        <input type="text" name="username" placeholder="juanp" required 
                               class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl focus:bg-white focus:border-primary outline-none font-bold text-slate-700 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Rol</label>
                        <select name="rol" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl focus:bg-white focus:border-primary outline-none font-bold text-slate-700 transition-all appearance-none">
                            <option value="operador">Operador</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Contraseña Temporal</label>
                    <input type="password" name="password" placeholder="••••••••" required 
                           class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl focus:bg-white focus:border-primary outline-none font-bold text-slate-700 transition-all">
                </div>

                <button type="submit" class="w-full bg-primary text-white font-black py-5 rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 text-sm tracking-wider uppercase flex items-center justify-center gap-3 active:scale-95">
                    <span class="material-symbols-outlined">person_add</span>
                    Confirmar Registro
                </button>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('hidden');
        }

        // Cerrar modal al hacer click fuera
        window.onclick = function(event) {
            const modal = document.getElementById('modalRegistro');
            if (event.target == modal) {
                modal.classList.add('hidden');
            }
        }

        // Navegación activa
        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = window.location.pathname.split('/').pop() || 'usuarios.php';
            document.querySelectorAll('aside nav a').forEach(link => {
                if(link.getAttribute('href') === currentPath) {
                    link.classList.add('bg-primary/10', 'text-primary', 'font-bold');
                }
            });
        });
    </script>
</body>
</html>