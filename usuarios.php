<?php
require_once 'functions.php';
require_admin(); 

$mensaje = '';

// Función para validar nivel de seguridad de contraseña
function validarPassword($pass) {
    return strlen($pass) > 6 && preg_match('/[A-Z]/', $pass) && preg_match('/[0-9]/', $pass);
}

// --- LÓGICA DE PROCESAMIENTO (REGISTRO Y ACTUALIZACIÓN) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $nombre = trim($_POST['nombre']);
    $username = trim($_POST['username']);
    $correo = trim($_POST['correo']);
    $rol = $_POST['rol'];
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $password = $_POST['password'];

    if ($_POST['accion'] === 'registrar') {
        $check = $mysqli->prepare("SELECT id FROM usuarios WHERE username = ? OR correo = ?");
        $check->bind_param('ss', $username, $correo);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            $mensaje = '<div class="bg-amber-50 text-amber-700 p-4 rounded-2xl mb-6 border border-amber-100 flex items-center gap-3 shadow-sm"><span class="material-symbols-outlined text-lg">warning</span><p class="text-xs font-bold">El username o el correo ya están en uso.</p></div>';
        } elseif (!validarPassword($password)) {
            $mensaje = '<div class="bg-red-50 text-red-700 p-4 rounded-2xl mb-6 border border-red-100 flex items-center gap-3 shadow-sm"><span class="material-symbols-outlined text-lg">security</span><p class="text-xs font-bold">La contraseña no cumple los requisitos de seguridad.</p></div>';
        } else {
            $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO usuarios (nombre, username, correo, password_hash, rol) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $nombre, $username, $correo, $password_encriptada, $rol);
            if ($stmt->execute()) {
                $mensaje = '<div class="bg-green-50 text-green-700 p-4 rounded-2xl mb-6 border border-green-100 flex items-center gap-3 shadow-sm"><span class="material-symbols-outlined text-lg">person_add</span><p class="text-xs font-bold">¡Usuario creado exitosamente!</p></div>';
            }
        }
    } elseif ($_POST['accion'] === 'editar') {
        if (!empty($password)) {
            if (!validarPassword($password)) {
                $mensaje = '<div class="bg-red-50 text-red-700 p-4 rounded-2xl mb-6 border border-red-100 flex items-center gap-3 shadow-sm"><span class="material-symbols-outlined text-lg">security</span><p class="text-xs font-bold">La nueva contraseña es débil.</p></div>';
            } else {
                $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $mysqli->prepare("UPDATE usuarios SET nombre=?, username=?, correo=?, rol=?, password_hash=? WHERE id=?");
                $stmt->bind_param('sssssi', $nombre, $username, $correo, $rol, $password_encriptada, $id);
                $stmt->execute();
                $mensaje = '<div class="bg-blue-50 text-blue-700 p-4 rounded-2xl mb-6 border border-blue-100 flex items-center gap-3 shadow-sm"><span class="material-symbols-outlined text-lg">edit</span><p class="text-xs font-bold">Usuario y contraseña actualizados.</p></div>';
            }
        } else {
            $stmt = $mysqli->prepare("UPDATE usuarios SET nombre=?, username=?, correo=?, rol=? WHERE id=?");
            $stmt->bind_param('ssssi', $nombre, $username, $correo, $rol, $id);
            $stmt->execute();
            $mensaje = '<div class="bg-blue-50 text-blue-700 p-4 rounded-2xl mb-6 border border-blue-100 flex items-center gap-3 shadow-sm"><span class="material-symbols-outlined text-lg">edit</span><p class="text-xs font-bold">Usuario actualizado correctamente.</p></div>';
        }
    }
}

// --- LÓGICA DE ELIMINACIÓN ---
if (isset($_GET['confirmar_eliminar'])) {
    $id_a_borrar = intval($_GET['confirmar_eliminar']);
    if ($id_a_borrar === $_SESSION['user']['id']) {
        $mensaje = '<div class="bg-red-50 text-red-700 p-4 rounded-2xl mb-6 border border-red-100 flex items-center gap-3"><span class="material-symbols-outlined text-lg">error</span><p class="font-bold text-xs">No puedes eliminar tu propia cuenta.</p></div>';
    } else {
        $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param('i', $id_a_borrar);
        $stmt->execute();
        $mensaje = '<div class="bg-slate-900 text-white p-4 rounded-2xl mb-6 flex items-center gap-3"><span class="material-symbols-outlined text-red-400 text-lg">delete_sweep</span><p class="text-xs font-bold">Usuario eliminado correctamente.</p></div>';
    }
}

$res_usuarios = $mysqli->query("SELECT id, username, correo, nombre, rol FROM usuarios ORDER BY rol ASC, nombre ASC");
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>ParkControl - Usuarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#005ac1' } } } }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modal-overlay { background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-[#f1f3f9] min-h-screen">
    <div class="flex">
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 pl-24 pr-6 py-8">
            <header class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-[10px] font-black text-primary uppercase tracking-widest mb-1">Configuración</h2>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Usuarios</h1>
                </div>
                <button onclick="abrirModalRegistro()" class="bg-primary text-white px-6 py-3 rounded-xl text-xs font-bold shadow-lg flex items-center gap-2 hover:bg-blue-700 transition-all">
                    <span class="material-symbols-outlined text-sm">add</span> Registrar Nuevo
                </button>
            </header>

            <?php echo $mensaje; ?>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200/50 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 border-b border-slate-100 text-slate-400 text-[10px] uppercase font-black tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Usuario</th>
                            <th class="px-6 py-4">Correo</th>
                            <th class="px-6 py-4 text-center">Nivel</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php while($u = $res_usuarios->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs uppercase"><?php echo substr($u['nombre'], 0, 1); ?></div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm leading-tight"><?php echo $u['nombre']; ?></p>
                                        <p class="text-[10px] text-slate-400 italic leading-tight">@<?php echo $u['username']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 font-medium"><?php echo $u['correo']; ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php 
                                    $rol_clase = 'bg-emerald-50 text-emerald-600 border border-emerald-100'; // Default Cliente
                                    if($u['rol'] == 'admin') $rol_clase = 'bg-indigo-50 text-indigo-600 border border-indigo-100';
                                    if($u['rol'] == 'operador') $rol_clase = 'bg-blue-50 text-blue-600 border border-blue-100';
                                ?>
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider <?php echo $rol_clase; ?>">
                                    <?php echo $u['rol']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick='abrirModalEditar(<?php echo json_encode($u); ?>)' class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-300 hover:text-primary hover:bg-blue-50 transition-all">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                    <?php if($u['id'] !== $_SESSION['user']['id']): ?>
                                        <button onclick="confirmarEliminar(<?php echo $u['id']; ?>, '<?php echo $u['nombre']; ?>')" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODAL FORMULARIO -->
    <div id="modalUsuario" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 modal-overlay">
        <div class="bg-white w-full max-w-sm rounded-[1.5rem] shadow-2xl overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <div>
                    <h3 id="modalTitulo" class="font-black text-lg text-slate-800 mb-1">Usuario</h3>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Credenciales de acceso</p>
                </div>
                <button onclick="cerrarModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400"><span class="material-symbols-outlined">close</span></button>
            </div>
            
            <form id="formUsuario" action="usuarios.php" method="POST" onsubmit="return validarSeguridad()" class="p-6 space-y-3">
                <input type="hidden" name="accion" id="formAccion">
                <input type="hidden" name="id" id="usuarioId">
                
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase">Nombre Completo</label>
                    <input type="text" name="nombre" id="inputNombre" required class="w-full px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl focus:bg-white focus:border-primary outline-none font-semibold text-slate-700 text-xs transition-all">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Username</label>
                        <input type="text" name="username" id="inputUsername" required class="w-full px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl focus:bg-white focus:border-primary outline-none font-semibold text-slate-700 text-xs">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase">Rol</label>
                        <select name="rol" id="inputRol" class="w-full px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl outline-none font-semibold text-slate-700 text-xs appearance-none">
                            <option value="operador">Operador</option>
                            <option value="admin">Administrador</option>
                            <option value="cliente">Cliente/Usuario</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase">Correo Electrónico</label>
                    <input type="email" name="correo" id="inputCorreo" required class="w-full px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl focus:bg-white focus:border-primary outline-none text-xs">
                </div>

                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase">Contraseña</label>
                    <input type="password" name="password" id="inputPassword" placeholder="••••••••" class="w-full px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl focus:bg-white focus:border-primary outline-none text-xs">
                    <div id="passwordError" class="text-[8px] text-red-500 font-bold mt-1 uppercase hidden">Min 7 chars, 1 Mayus y 1 Núm</div>
                    <p id="hintPass" class="hidden text-[8px] text-primary font-bold mt-1 uppercase">* Solo para cambiarla</p>
                </div>

                <button type="submit" class="w-full bg-primary text-white font-black py-3.5 rounded-xl mt-2 shadow-lg text-[10px] uppercase tracking-widest flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm" id="btnIcono">save</span>
                    <span id="btnTexto">Guardar Usuario</span>
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL ELIMINAR PERSONALIZADO -->
    <div id="modalEliminar" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4 modal-overlay">
        <div class="bg-white w-full max-w-xs rounded-[2rem] shadow-2xl p-8 text-center border border-slate-100">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">delete_forever</span>
            </div>
            <h3 class="text-lg font-black text-slate-800 mb-2">¿Estás seguro?</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">Estás a punto de eliminar a <b id="nombreAEliminar" class="text-slate-900"></b>. Esta acción no tiene vuelta atrás.</p>
            <div class="flex flex-col gap-2">
                <a id="linkConfirmarEliminar" href="#" class="bg-red-500 text-white font-black py-3 rounded-xl text-[10px] uppercase tracking-widest hover:bg-red-600 transition-all">Sí, Eliminar Usuario</a>
                <button onclick="cerrarModalEliminar()" class="text-slate-400 font-black py-3 text-[10px] uppercase tracking-widest hover:text-slate-600 transition-all">Mejor no, Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalUsuario');
        const modalDel = document.getElementById('modalEliminar');
        const form = document.getElementById('formUsuario');

        function validarSeguridad() {
            const pass = document.getElementById('inputPassword').value;
            const accion = document.getElementById('formAccion').value;
            const errorDiv = document.getElementById('passwordError');

            if(accion === 'editar' && pass === '') return true;

            const regex = /^(?=.*[A-Z])(?=.*\d).{7,}$/;
            if(!regex.test(pass)) {
                errorDiv.classList.remove('hidden');
                return false;
            }
            errorDiv.classList.add('hidden');
            return true;
        }

        function abrirModalRegistro() {
            form.reset();
            document.getElementById('modalTitulo').innerText = "Nuevo Usuario";
            document.getElementById('formAccion').value = "registrar";
            document.getElementById('inputPassword').required = true;
            document.getElementById('hintPass').classList.add('hidden');
            document.getElementById('passwordError').classList.add('hidden');
            modal.classList.remove('hidden');
        }

        function abrirModalEditar(u) {
            document.getElementById('modalTitulo').innerText = "Editar Usuario";
            document.getElementById('formAccion').value = "editar";
            document.getElementById('usuarioId').value = u.id;
            document.getElementById('inputNombre').value = u.nombre;
            document.getElementById('inputUsername').value = u.username;
            document.getElementById('inputCorreo').value = u.correo;
            document.getElementById('inputRol').value = u.rol;
            document.getElementById('inputPassword').required = false;
            document.getElementById('hintPass').classList.remove('hidden');
            document.getElementById('passwordError').classList.add('hidden');
            modal.classList.remove('hidden');
        }

        function confirmarEliminar(id, nombre) {
            document.getElementById('nombreAEliminar').innerText = nombre;
            document.getElementById('linkConfirmarEliminar').href = `usuarios.php?confirmar_eliminar=${id}`;
            modalDel.classList.remove('hidden');
        }

        function cerrarModal() { modal.classList.add('hidden'); }
        function cerrarModalEliminar() { modalDel.classList.add('hidden'); }
    </script>
</body>
</html>