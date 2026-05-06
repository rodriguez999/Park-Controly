# 🅿️ Park-Control — Rama `prototype`

> **⚠️ Nota:** Esta rama es un prototipo experimental. Su propósito es explorar y validar ideas antes de desarrollar la versión 0 oficial del sistema. El código aquí presente **no está listo para producción**.

---

## 📌 Descripción

**Park-Control** es un sistema de gestión de parqueos desarrollado en PHP. Esta rama (`prototype`) sirve como campo de pruebas para definir la arquitectura, flujo de usuarios y funcionalidades base que formarán parte de la `v0` del proyecto.

---

## 🗂️ Estructura del Proyecto

```
park-controly/
├── images/                  # Recursos de imágenes
├── node_modules/            # Dependencias de Node.js
├── .htaccess                # Configuración del servidor Apache
├── .prettierrc.json         # Configuración de formato de código
├── config.php               # Configuración general (BD, constantes)
├── db_parkeo.sql            # Script SQL — base de datos inicial
├── park-controly-bd.sql     # Script SQL — base de datos principal
├── entrada.php              # Registro de entrada de vehículos
├── salida.php               # Registro de salida de vehículos
├── ticket.php               # Generación de ticket de entrada
├── ticket_salida.php        # Generación de ticket de salida
├── vehiculos.php            # Gestión de vehículos
├── historial.php            # Historial de movimientos
├── functions.php            # Funciones auxiliares globales
├── menu.php                 # Menú principal del sistema
├── index.php                # Punto de entrada principal
├── login.html               # Página de inicio de sesión
├── register.html            # Página de registro de usuarios
├── registrar.php            # Lógica de registro
├── logout.php               # Cierre de sesión
├── package.json             # Dependencias de Node.js
└── package-lock.json        # Lock de dependencias
```

---

## ⚙️ Tecnologías Utilizadas

| Tecnología | Uso |
|---|---|
| PHP | Lógica del servidor y backend |
| MySQL | Base de datos del sistema |
| HTML/CSS | Interfaz de usuario |
| JavaScript / Node.js | Utilidades front-end y herramientas de desarrollo |
| Apache (.htaccess) | Configuración del servidor web |

---

## 🚀 Instalación y Configuración Local

### Prerrequisitos

- PHP >= 7.4
- MySQL >= 5.7
- Servidor Apache (recomendado: XAMPP o Laragon)
- Node.js (para herramientas de desarrollo)

### Pasos

1. **Clonar el repositorio y cambiar a esta rama:**
   ```bash
   git clone https://github.com/rodriguez999/Park-Controly.git
   cd park-controly
   git checkout prototype
   ```

2. **Importar la base de datos:**
   - Abrir phpMyAdmin o tu cliente MySQL preferido.
   - Crear una base de datos llamada `park_controly`.
   - Importar el archivo `park-controly-bd.sql` (o `db_parkeo.sql` si es la versión inicial).

3. **Configurar la conexión a la BD:**
   - Editar `config.php` con tus credenciales locales:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'park_controly');
     ```

4. **Instalar dependencias de Node.js (opcional):**
   ```bash
   npm install
   ```

5. **Colocar el proyecto en la carpeta del servidor** (ej. `htdocs` en XAMPP) y acceder desde el navegador:
   ```
   http://localhost/park-controly/
   ```

---

## 🔑 Funcionalidades Prototipadas

- [x] Autenticación de usuarios (login / registro / logout)
- [x] Registro de entrada de vehículos
- [x] Registro de salida de vehículos
- [x] Generación de tickets (entrada y salida)
- [x] Historial de movimientos
- [x] Gestión de vehículos
- [x] Menú de navegación principal

---

## 🧪 Objetivo de esta Rama

Esta rama `prototype` tiene como finalidad:

- Validar el flujo completo de entrada y salida de vehículos.
- Probar la estructura de la base de datos antes de definirla formalmente.
- Identificar mejoras de UX/UI para aplicar en la `v0`.
- Experimentar con la arquitectura de archivos PHP.

> Los hallazgos y decisiones tomadas aquí serán documentados y trasladados a la rama principal para el desarrollo de la versión 0.

---

## 🛣️ Próximos Pasos (hacia la v0)

- [ ] Refactorizar el código PHP con una arquitectura más ordenada (MVC o similar)
- [ ] Implementar validaciones y manejo de errores robusto
- [ ] Mejorar la seguridad (prepared statements, sanitización de inputs)
- [ ] Diseñar una interfaz de usuario más pulida
- [ ] Definir el esquema final de la base de datos
- [ ] Agregar sistema de roles (administrador, operador)

---

