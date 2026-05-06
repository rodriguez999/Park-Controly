# 🚗 Park Control - Versión 2.0

Sistema avanzado de gestión de parqueo desarrollado en PHP con funcionalidades mejoradas para el control integral de entradas, salidas, usuarios y reportes administrativos.

## 📋 Descripción

Park Control v2.0 es una versión mejorada del sistema de gestión de estacionamientos, que incorpora nuevas funcionalidades como gestión avanzada de usuarios, sistema de perfiles personalizables, reportes detallados, y una interfaz de usuario más completa y profesional.

## 🚀 Características

- ✅ **Gestión Completa de Vehículos**: Registro, consulta y administración avanzada
- 🎫 **Sistema de Tickets Mejorado**: Tickets de entrada y salida con más información
- 💰 **Procesamiento de Pagos**: Sistema automatizado de cálculo de tarifas
- 👥 **Gestión de Usuarios Avanzada**: Múltiples roles y perfiles
- 📊 **Sistema de Reportes**: Generación de reportes detallados y estadísticas
- 🔐 **Seguridad Mejorada**: Autenticación robusta y control de sesiones
- 👤 **Perfiles de Usuario**: Gestión de perfiles personalizables
- 📝 **Historial Detallado**: Seguimiento completo de todas las operaciones
- 🎨 **Interfaz Mejorada**: Sidebar y menú de navegación optimizados

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP
- **Base de Datos**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Servidor Web**: Apache (configurado con .htaccess)
- **Gestión de Dependencias**: NPM
- **Code Formatting**: Prettier

## 📁 Estructura del Proyecto

```
PARK-CONTROL/
├── images/                    # Recursos gráficos e imágenes
├── node_modules/             # Dependencias de Node.js
├── .htaccess                 # Configuración de Apache
├── .prettierrc.json          # Configuración de Prettier
├── config.php                # Configuración general del sistema
├── configuracion.php         # Parámetros y configuraciones
├── db_parkeo.sql            # Estructura base de datos
├── entrada.php              # Registro de entradas de vehículos
├── functions.php            # Funciones auxiliares del sistema
├── historial.php            # Historial general de movimientos
├── index.php                # Página principal/dashboard
├── login.html               # Página de inicio de sesión
├── logout.php               # Cierre de sesión
├── menu.php                 # Menú de navegación principal
├── package-lock.json        # Lock de dependencias NPM
├── package.json             # Configuración de dependencias
├── park-controly-bd.sql     # Base de datos completa
├── perfil.php               # Gestión de perfil de usuario
├── README.md                # Documentación del proyecto
├── register.html            # Página de registro de usuarios
├── registrar.php            # Procesamiento de registro
├── reportes.php             # Generación de reportes
├── salida.php               # Registro de salidas de vehículos
├── sidebar.php              # Barra lateral de navegación
├── ticket_entrada.php       # Generación de ticket de entrada
├── ticket_salida.php        # Generación de ticket de salida
├── ticket.php               # Gestión general de tickets
├── update_profile.php       # Actualización de perfil
├── usuarios.php             # Administración de usuarios
└── vehiculos.php            # Administración de vehículos
```

## 💾 Instalación

### Requisitos Previos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor Apache con mod_rewrite habilitado
- Node.js 14+ y NPM (para dependencias frontend)

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
   git clone https://github.com/rodriguez999/Park-Controly.git
   cd park-control
   git checkout v2.0
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
   
   # O importar la base de datos completa con datos de ejemplo
   mysql -u root -p park_control < park-controly-bd.sql
```

3. **Configurar la conexión a la base de datos**
   
   Editar el archivo `config.php`:
```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_contraseña');
   define('DB_NAME', 'park_control');
   ?>
```

4. **Configurar parámetros del sistema**
   
   Editar `configuracion.php` para ajustar:
   - Tarifas de estacionamiento
   - Horarios de operación
   - Capacidad del parqueo
   - Tipos de vehículos

5. **Instalar dependencias**
```bash
   npm install
```

6. **Configurar permisos**
```bash
   chmod 755 /ruta/del/proyecto
   chmod 644 *.php *.html
   chmod 755 images/
```

7. **Acceder al sistema**
   
   Abrir en el navegador: `http://localhost/park-control`

## 👤 Uso del Sistema

### Primer Acceso

1. Navegar a `login.html`
2. Registrarse en `register.html` si no tienes cuenta
3. Iniciar sesión con tus credenciales
4. Serás redirigido al dashboard principal

### Funcionalidades Principales

#### 🚗 Registrar Entrada de Vehículo
1. Acceder a `entrada.php` desde el menú
2. Ingresar datos del vehículo (placa, tipo, propietario)
3. El sistema genera automáticamente un ticket con código único
4. Visualizar el ticket en `ticket_entrada.php`

#### 🚪 Registrar Salida de Vehículo
1. Acceder a `salida.php`
2. Buscar el vehículo por placa o código de ticket
3. El sistema calcula automáticamente el tiempo y monto
4. Procesar el pago
5. Generar ticket de salida en `ticket_salida.php`

#### 📊 Generar Reportes
1. Ir a `reportes.php`
2. Seleccionar tipo de reporte:
   - Reportes diarios
   - Reportes mensuales
   - Estadísticas de uso
   - Ingresos generados
3. Exportar o imprimir el reporte

#### 👤 Gestionar Perfil
1. Acceder a `perfil.php`
2. Actualizar información personal
3. Cambiar contraseña
4. Configurar preferencias

#### 👥 Administrar Usuarios (Solo Admin)
1. Ir a `usuarios.php`
2. Ver lista de usuarios registrados
3. Crear, editar o eliminar usuarios
4. Asignar roles y permisos

#### 🚙 Gestionar Vehículos
1. Acceder a `vehiculos.php`
2. Ver, agregar, editar o eliminar vehículos
3. Consultar historial de cada vehículo

#### 📜 Consultar Historial
1. Ir a `historial.php`
2. Filtrar por:
   - Fecha
   - Vehículo
   - Usuario
   - Tipo de operación
3. Exportar registros

## 📊 Base de Datos

### Archivos SQL Disponibles

- **`db_parkeo.sql`**: Estructura base de la base de datos
- **`park-controly-bd.sql`**: Base de datos completa con datos de ejemplo

### Tablas Principales

- **usuarios**: Información de usuarios del sistema con roles
- **vehiculos**: Registro completo de vehículos
- **entradas**: Log detallado de entradas al parqueo
- **salidas**: Log detallado de salidas del parqueo
- **tickets**: Información de tickets generados
- **pagos**: Registro de transacciones y pagos
- **configuracion**: Parámetros del sistema
- **historial**: Auditoría de todas las operaciones

## 🔧 Configuración Avanzada

### Archivo configuracion.php

Personaliza el comportamiento del sistema:

```php
<?php
// Tarifas
define('TARIFA_HORA', 50);
define('TARIFA_DIA', 200);
define('TARIFA_MENSUAL', 3000);

// Capacidad
define('CAPACIDAD_TOTAL', 100);
define('CAPACIDAD_MOTOS', 30);

// Horarios
define('HORA_APERTURA', '06:00');
define('HORA_CIERRE', '22:00');
?>
```

### Personalización de la Interfaz

- **menu.php**: Modifica la estructura del menú principal
- **sidebar.php**: Personaliza la barra lateral
- Archivos CSS en `images/` o carpeta de estilos

### Formateo de Código

El proyecto usa Prettier para mantener un código consistente:

```bash
npm run format
```

## 🔒 Seguridad

### Características de Seguridad Implementadas

- ✅ Validación de sesiones en todas las páginas
- ✅ Sanitización de todas las entradas de usuario
- ✅ Protección contra SQL Injection con prepared statements
- ✅ Control de acceso basado en roles (RBAC)
- ✅ Encriptación de contraseñas con bcrypt
- ✅ Protección CSRF en formularios
- ✅ Headers de seguridad en .htaccess
- ✅ Validación del lado del servidor y cliente

## 🐛 Solución de Problemas

### Error de conexión a la base de datos
```bash
# Verificar que MySQL esté corriendo
sudo systemctl status mysql

# Verificar credenciales en config.php
# Asegurar que la base de datos existe
mysql -u root -p -e "SHOW DATABASES;"
```

### Errores de permisos
```bash
# Dar permisos correctos
sudo chown -R www-data:www-data /var/www/park-control
sudo chmod -R 755 /var/www/park-control
```

### .htaccess no funciona
```apache
# En apache2.conf o httpd.conf, asegurar:
<Directory /var/www/park-control>
    AllowOverride All
</Directory>

# Habilitar mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Sesiones no persisten
- Verificar permisos en `/tmp` o directorio de sesiones de PHP
- Revisar configuración de `session.save_path` en php.ini

## 📈 Novedades de la Versión 2.0

### ✨ Nuevas Características

- **Sistema de Perfiles**: Los usuarios pueden gestionar su información personal
- **Reportes Avanzados**: Múltiples tipos de reportes con filtros
- **Sidebar Mejorado**: Navegación más intuitiva y accesible
- **Gestión de Usuarios**: Panel de administración completo
- **Configuración Dinámica**: Parámetros ajustables sin modificar código
- **Tickets Mejorados**: Mayor información y mejor formato
- **Historial Detallado**: Trazabilidad completa de operaciones

### 🔄 Mejoras sobre v1.0

- Interfaz de usuario completamente rediseñada
- Mejor organización del código con `functions.php` expandido
- Sistema de configuración centralizado
- Más validaciones y controles de seguridad
- Código formateado con Prettier
- Mejor estructura de base de datos

## 🎯 Casos de Uso

### Para Administradores
- Gestión completa de usuarios y permisos
- Generación de reportes financieros
- Configuración de tarifas y parámetros
- Monitoreo en tiempo real de ocupación

### Para Operadores
- Registro rápido de entradas y salidas
- Consulta de vehículos en el parqueo
- Procesamiento de pagos
- Impresión de tickets

### Para Clientes
- Consulta de su historial de visitas
- Actualización de perfil
- Vista de vehículos registrados

### Guía de Estilo

- Seguir las reglas de Prettier definidas en `.prettierrc.json`
- Comentar código complejo
- Escribir nombres de variables y funciones descriptivos
- Mantener funciones pequeñas y enfocadas

## 📝 Changelog

### [2.0.0] - 2024

#### Añadido
- Sistema de perfiles de usuario
- Panel de reportes avanzados
- Sidebar de navegación
- Gestión completa de usuarios
- Archivo de configuración centralizado
- Tickets de entrada y salida mejorados
- Sistema de actualización de perfil

#### Mejorado
- Interfaz de usuario más intuitiva
- Mejor organización del código
- Seguridad reforzada
- Validación de datos mejorada
- Estructura de base de datos optimizada

#### Corregido
- Problemas de sesión
- Errores en cálculo de tarifas
- Validaciones de formularios

