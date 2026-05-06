# 🚗 Park Control - Versión 1.0

Sistema de gestión de parqueo desarrollado en PHP para el control básico de entradas, salidas y administración de vehículos.

## 📋 Descripción

Park Control v1.0 es la versión inicial de una aplicación web diseñada para facilitar la gestión de estacionamientos, permitiendo el registro de vehículos, control de entradas y salidas, generación de tickets básicos y gestión de usuarios.

## 🚀 Características

- ✅ **Gestión de Vehículos**: Registro y administración de vehículos
- 🎫 **Sistema de Tickets**: Generación de tickets de entrada y salida
- 👥 **Gestión de Usuarios**: Sistema de autenticación básico
- 📊 **Historial**: Consulta de movimientos del parqueo
- 🔐 **Autenticación**: Login y registro de usuarios
- 💰 **Salida de Vehículos**: Procesamiento de salida y pagos

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP
- **Base de Datos**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Servidor Web**: Apache (configurado con .htaccess)
- **Gestión de Dependencias**: NPM

## 📁 Estructura del Proyecto
```
PARK-CONTROL/
├── images/                # Recursos gráficos e imágenes
├── node_modules/         # Dependencias de Node.js
├── .htaccess            # Configuración de Apache
├── config.php           # Configuración de conexión a BD
├── db_parkeo.sql       # Estructura de base de datos
├── entrada.php         # Registro de entradas de vehículos
├── functions.php       # Funciones auxiliares del sistema
├── historial.php       # Historial de movimientos
├── index.php           # Página principal/dashboard
├── login.html          # Página de inicio de sesión
├── logout.php          # Cierre de sesión
├── menu.php            # Menú de navegación principal
├── package-lock.json   # Lock de dependencias NPM
├── package.json        # Configuración de dependencias
├── README.md           # Documentación del proyecto
├── register.html       # Página de registro de usuarios
├── salida.php          # Registro de salidas de vehículos
├── ticket_salida.php   # Generación de ticket de salida
├── ticket.php          # Gestión de tickets
└── vehiculos.php       # Administración de vehículos
```
## 💾 Instalación

### Requisitos Previos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor Apache con mod_rewrite habilitado
- Node.js y NPM (opcional, para dependencias frontend)

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
   git clone https://github.com/rodriguez999/Park-Controly.git
   cd park-control
```

2. **Configurar la base de datos**
```bash
   # Acceder a MySQL
   mysql -u root -p
   
   # Crear la base de datos
   CREATE DATABASE park_control;
   exit;
   
   # Importar la estructura
   mysql -u root -p park_control < db_parkeo.sql
```

3. **Configurar la conexión a la base de datos**
   
   Editar el archivo `config.php` con tus credenciales:
```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_contraseña');
   define('DB_NAME', 'park_control');
   ?>
```

4. **Instalar dependencias (opcional)**
```bash
   npm install
```

5. **Configurar permisos del servidor**
```bash
   chmod 755 /ruta/del/proyecto
   chmod 644 *.php
   chmod 644 *.html
```

6. **Acceder al sistema**
   
   Abrir en el navegador: `http://localhost/park-control`

## 👤 Uso del Sistema

### Primer Acceso

1. Navegar a `login.html` en tu navegador
2. Si no tienes cuenta, hacer clic en "Registrarse" para ir a `register.html`
3. Completar el formulario de registro
4. Iniciar sesión con las credenciales creadas

### Funcionalidades Principales

#### Registrar Entrada de Vehículo
1. Acceder a `entrada.php`
2. Ingresar los datos del vehículo (placa, tipo, etc.)
3. El sistema genera automáticamente un ticket de entrada

#### Registrar Salida de Vehículo
1. Acceder a `salida.php`
2. Buscar el vehículo por placa
3. El sistema calcula el tiempo y genera el ticket de salida

#### Consultar Historial
1. Ir a `historial.php`
2. Visualizar todos los movimientos del parqueo
3. Filtrar por fecha o vehículo

#### Gestión de Vehículos
1. Acceder a `vehiculos.php`
2. Ver, editar o eliminar registros de vehículos

## 📊 Base de Datos

### Archivo SQL

- **`db_parkeo.sql`**: Contiene la estructura completa de la base de datos

### Tablas Principales

- **usuarios**: Almacena información de usuarios del sistema
- **vehiculos**: Registro de todos los vehículos
- **entradas**: Log de entradas al parqueo
- **salidas**: Log de salidas del parqueo
- **tickets**: Información de tickets generados

## 🔧 Configuración

### Configuración de Apache (.htaccess)

El archivo `.htaccess` incluye:
- Reglas de reescritura de URL
- Configuración de seguridad
- Redirecciones

### Funciones Auxiliares (functions.php)

Contiene funciones reutilizables como:
- Validación de datos
- Formateo de fechas
- Cálculos de tiempo y tarifas
- Consultas comunes a la base de datos

## 🔒 Seguridad

- Validación de sesiones en todas las páginas protegidas
- Sanitización de entradas de usuario
- Protección contra SQL Injection mediante consultas preparadas
- Control de acceso basado en roles

## 🐛 Solución de Problemas

### Error de conexión a la base de datos
- Verificar credenciales en `config.php`
- Asegurar que MySQL esté corriendo
- Comprobar que la base de datos existe

### Páginas en blanco
- Habilitar `display_errors` en PHP
- Revisar logs de Apache
- Verificar permisos de archivos

### .htaccess no funciona
- Verificar que mod_rewrite esté habilitado
- Revisar configuración de Apache (AllowOverride All)

## 📈 Próximas Mejoras (v2.0)

- [ ] Sistema de reportes avanzados
- [ ] Múltiples perfiles de usuario
- [ ] Gestión de clientes frecuentes
- [ ] Dashboard con estadísticas en tiempo real
- [ ] Sistema de pagos más robusto
- [ ] Notificaciones por correo electrónico

⭐ **Park Control v1.0** - Sistema de Gestión de Parqueo