<?php
// Datos dinámicos del usuario
$user_name = $_SESSION['user']['nombre'] ?? 'Usuario';
$user_rol = $_SESSION['user']['rol'] ?? 'Operador';
$inicial = strtoupper(substr($user_name, 0, 1));
?>

<aside id="mainSidebar" class="fixed left-0 top-0 h-screen w-20 hover:w-64 bg-white border-r border-outline-variant/30 transition-all duration-300 z-50 flex flex-col group shadow-lg">
    
    <div class="p-6 mb-4 flex items-center gap-4">
        <div class="w-8 h-8 bg-primary rounded-lg flex-shrink-0 flex items-center justify-center text-white shadow-lg shadow-primary/20">
            <span class="material-symbols-outlined text-sm font-bold">local_parking</span>
        </div>
        <span class="sidebar-text font-headline font-black text-xl text-slate-800 tracking-tight whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">ParkControl</span>
    </div>

    <nav class="flex-1 px-4 space-y-2 overflow-y-auto overflow-x-hidden">
        <a href="menu.php" class="flex items-center gap-4 p-3 rounded-2xl text-slate-500 hover:bg-primary/5 hover:text-primary transition-all nav-link">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="sidebar-text font-bold text-sm tracking-tight whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Dashboard</span>
        </a>
        <a href="entrada.php" class="flex items-center gap-4 p-3 rounded-2xl text-slate-500 hover:bg-primary/5 hover:text-primary transition-all nav-link">
            <span class="material-symbols-outlined">login</span>
            <span class="sidebar-text font-bold text-sm tracking-tight whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Entrada</span>
        </a>
        <a href="salida.php" class="flex items-center gap-4 p-3 rounded-2xl text-slate-500 hover:bg-primary/5 hover:text-primary transition-all nav-link">
            <span class="material-symbols-outlined">logout</span>
            <span class="sidebar-text font-bold text-sm tracking-tight whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Salida</span>
        </a>
        <a href="historial.php" class="flex items-center gap-4 p-3 rounded-2xl text-slate-500 hover:bg-primary/5 hover:text-primary transition-all nav-link">
            <span class="material-symbols-outlined">history</span>
            <span class="sidebar-text font-bold text-sm tracking-tight whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Historial</span>
        </a>

        <?php if(is_admin()): ?>
            <div class="sidebar-text my-6 px-3 border-t border-slate-50 pt-6 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Administración</p>
            </div>
            <a href="usuarios.php" class="flex items-center gap-4 p-3 rounded-2xl text-slate-500 hover:bg-primary/5 hover:text-primary transition-all nav-link">
                <span class="material-symbols-outlined">group</span>
                <span class="sidebar-text font-bold text-sm tracking-tight whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Usuarios</span>
            </a>
            <a href="configuracion.php" class="flex items-center gap-4 p-3 rounded-2xl text-slate-500 hover:bg-primary/5 hover:text-primary transition-all nav-link">
                <span class="material-symbols-outlined">settings</span>
                <span class="sidebar-text font-bold text-sm tracking-tight whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Ajustes</span>
            </a>
        <?php endif; ?>
    </nav>

    <div class="p-4 border-t border-slate-100 relative">
        <button id="sidebarUserBtn" class="w-full flex items-center gap-3 p-2 rounded-2xl hover:bg-slate-50 transition-all focus:outline-none group/btn">
            <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-black shadow-md shadow-primary/20 flex-shrink-0 group-hover/btn:scale-105 transition-transform">
                <?php echo $inicial; ?>
            </div>
            <div class="sidebar-text text-left overflow-hidden opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <p class="text-xs font-black text-slate-800 truncate"><?php echo $user_name; ?></p>
                <p class="text-[9px] font-bold text-primary uppercase tracking-tighter">Mi Cuenta</p>
            </div>
        </button>

        <div id="sidebarUserMenu" class="hidden absolute left-[calc(100%+0.75rem)] bottom-4 w-60 bg-white shadow-2xl rounded-[1.5rem] border border-outline-variant/30 z-[70] overflow-hidden animate-in fade-in slide-in-from-left-4 duration-200">
            <div class="p-4 border-b border-slate-50 bg-slate-50/50">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Sesión activa</p>
                <p class="text-sm font-bold text-slate-800 truncate"><?php echo $user_name; ?></p>
            </div>
            <ul class="p-2">
                <li>
                    <a href="perfil.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-slate-600 hover:bg-primary/5 hover:text-primary transition-all font-medium">
                        <span class="material-symbols-outlined text-lg opacity-70">account_circle</span>
                        Gestionar Perfil
                    </a>
                </li>
                <li>
                    <a href="logout.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-500 hover:bg-red-50 transition-all font-bold">
                        <span class="material-symbols-outlined text-lg">power_settings_new</span>
                        Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>

<script>
    const sideBtn = document.getElementById('sidebarUserBtn');
    const sideMenu = document.getElementById('sidebarUserMenu');
    const sidebar = document.getElementById('mainSidebar');

    if(sideBtn && sideMenu) {
        sideBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sideMenu.classList.toggle('hidden');
            
            // Si el menú está abierto, forzamos que el sidebar se vea "activo"
            if(!sideMenu.classList.contains('hidden')) {
                sideBtn.classList.add('bg-slate-100');
            } else {
                sideBtn.classList.remove('bg-slate-100');
            }
        });

        document.addEventListener('click', (e) => {
            if (!sideBtn.contains(e.target) && !sideMenu.contains(e.target)) {
                sideMenu.classList.add('hidden');
                sideBtn.classList.remove('bg-slate-100');
            }
        });
    }

    // Lógica de resaltado de links corregida
    document.addEventListener('DOMContentLoaded', () => {
        const currentPath = window.location.pathname.split('/').pop() || 'menu.php';
        document.querySelectorAll('.nav-link').forEach(link => {
            if(link.getAttribute('href') === currentPath) {
                link.classList.add('bg-primary/10', 'text-primary', 'font-black');
                link.classList.remove('text-slate-500');
            } else {
                // Aseguramos que los demás no tengan estilos raros
                link.classList.remove('bg-primary/10', 'text-primary', 'font-black');
                link.classList.add('text-slate-500');
            }
        });
    });
</script>