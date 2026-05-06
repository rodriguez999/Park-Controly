# 🚗 Park Control

Sistema de gestión de parqueo desarrollado en PHP para el control de entradas, salidas y administración de vehículos.

## 📋 Descripción

Park Control es una aplicación web diseñada para facilitar la gestión de estacionamientos, permitiendo el registro de vehículos, control de entradas y salidas, generación de tickets, procesamiento de pagos y reportes administrativos.

## 🚀 Características

- ✅ **Gestión de Vehículos**: Registro y consulta del historial de vehículos
- 🎫 **Sistema de Tickets**: Generación automática de tickets de entrada y salida
- 💰 **Procesamiento de Pagos**: Cálculo automático de tarifas según tiempo de estancia
- 📊 **Reportes**: Generación de reportes de uso y estadísticas
- 👥 **Gestión de Usuarios**: Sistema de autenticación y perfiles
- 🔒 **Seguridad**: Control de acceso y sesiones protegidas

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP
- **Base de Datos**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Servidor Web**: Apache (configurado con .htaccess)

## 📁 Estructura del Proyecto
PARK-CONTROL/
```
├── images/                    # Recursos gráficos
├── node_modules/             # Dependencias de Node.js
├── .htaccess                 # Configuración de Apache
├── .prettierrc.json          # Configuración de formato de código
├── config.php                # Configuración general del sistema
├── configuracion.php         # Parámetros de configuración
├── consulta_cliente.php      # Consulta de información de clientes
├── db_parkeo.sql            # Estructura de base de datos
├── entrada.php              # Registro de entradas de vehículos
├── functions.php            # Funciones auxiliares
├── historial_cliente.php    # Historial de clientes
├── historial.php            # Historial general
├── index.php                # Página principal
├── login.html               # Página de inicio de sesión
├── logout.php               # Cierre de sesión
├── menu.php                 # Menú de navegación
├── mis_vehiculos.php        # Gestión de vehículos del usuario
├── package.json             # Dependencias del proyecto
├── park-controly-bd.sql     # Base de datos completa
├── perfil.php               # Perfil de usuario
├── procesar_pago.php        # Procesamiento de pagos
├── register.html            # Página de registro
├── registrar.php            # Procesamiento de registro
├── reportes.php             # Generación de reportes
├── salida.php               # Registro de salidas de vehículos
├── sidebar.php              # Barra lateral de navegación
├── ticket_entrada.php       # Generación de ticket de entrada
├── ticket_salida.php        # Generación de ticket de salida
├── ticket.php               # Gestión de tickets
├── update_profile.php       # Actualización de perfil
├── usuarios.php             # Gestión de usuarios
└── vehiculos.php            # Administración de vehículos
```

## 💾 Instalación

### Requisitos Previos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor Apache
- Node.js (para dependencias frontend)

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
   git clone https://github.com/tu-usuario/park-control.git
   cd park-control
```

2. **Configurar la base de datos**
```bash
   # Crear la base de datos
   mysql -u root -p
   CREATE DATABASE park_control;
   exit;
   
   # Importar la estructura
   mysql -u root -p park_control < db_parkeo.sql
   # O importar la base de datos completa
   mysql -u root -p park_control < park-controly-bd.sql
```

3. **Configurar conexión a la base de datos**
   
   Editar el archivo `config.php` con tus credenciales:
```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_contraseña');
   define('DB_NAME', 'park_control');
```

4. **Instalar dependencias (opcional)**
```bash
   npm install
```

5. **Configurar permisos**
```bash
   chmod 755 /ruta/del/proyecto
   chmod 644 *.php
```

6. **Acceder al sistema**
   
   Abrir en el navegador: `http://localhost/park-control`

## 👤 Uso

### Primer Acceso

1. Acceder a `login.html`
2. Crear una cuenta nueva en `register.html`
3. Iniciar sesión con las credenciales creadas

### Funcionalidades Principales

- **Registrar Entrada**: Ir a `entrada.php` y escanear o ingresar la placa del vehículo
- **Registrar Salida**: Ir a `salida.php` y procesar el pago correspondiente
- **Ver Historial**: Consultar en `historial.php` todos los movimientos
- **Generar Reportes**: Acceder a `reportes.php` para estadísticas

## 🔧 Configuración

### Parámetros del Sistema

Editar `configuracion.php` para ajustar:
- Tarifas por hora/día
- Horarios de operación
- Capacidad del parqueo
- Tipos de vehículos aceptados

### Archivo .htaccess

El sistema incluye configuración de Apache para:
- Redirecciones amigables
- Seguridad adicional
- Compresión de archivos

## 📊 Base de Datos

El sistema incluye dos archivos SQL:

- **`db_parkeo.sql`**: Estructura básica de la base de datos
- **`park-controly-bd.sql`**: Base de datos completa con datos de ejemplo

### Tablas Principales

- `usuarios`: Información de usuarios del sistema
- `vehiculos`: Registro de vehículos
- `tickets`: Tickets de entrada/salida
- `pagos`: Registro de transacciones
- `historial`: Log de movimientos
