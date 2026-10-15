# IMPRIMEX · Plataforma de Gestión de Pedidos

Proyecto en PHP + HTML + MySQL basado en el boceto (wireframe) de 6 pantallas:
1. Registro / Inicio de sesión
2. Panel Cliente
3. Crear Pedido
4. Estado del Pedido
5. Panel Empleado
6. Panel Administrador

> **Nota:** el proyecto detecta automáticamente si está en `http://localhost/` o en una
> subcarpeta como `http://localhost/imprimex/`, así que puedes copiarlo con cualquier
> nombre de carpeta y funcionará sin tocar código.

## Requisitos
- PHP 8.x
- MySQL / MariaDB
- Servidor local: XAMPP, WAMP, Laragon o `php -S`

## Cómo abrir el proyecto (con XAMPP / WAMP / Laragon)

1. Copia la carpeta `imprimex` dentro de la carpeta pública de tu servidor:
   - XAMPP (Windows): `C:\xampp\htdocs\imprimex`
   - Laragon: `C:\laragon\www\imprimex`
   - WAMP: `C:\wamp64\www\imprimex`
2. Abre phpMyAdmin y crea la base de datos importando el archivo `database/schema.sql`
   (esto crea la base `imprimex`, las tablas y usuarios de prueba).
3. Revisa `config/db.php` y ajusta `DB_HOST`, `DB_USER`, `DB_PASS` si es necesario
   (por defecto usa `root` sin contraseña, típico de XAMPP/Laragon).
4. Inicia Apache y MySQL desde el panel de tu servidor.
5. Abre en el navegador: `http://localhost/imprimex/`

## Cómo abrir el proyecto con el servidor integrado de PHP (sin Apache)

Desde la carpeta `imprimex`, ejecuta:

```bash
php -S localhost:8000
```

Y abre `http://localhost:8000/` en el navegador. (De todas formas necesitas MySQL corriendo y la base de datos importada).

## Usuarios de prueba

| Rol            | Correo                | Contraseña   |
|----------------|------------------------|--------------|
| Administrador  | admin@imprimex.com     | admin123     |
| Empleado       | andrea@imprimex.com    | empleado123  |
| Cliente        | cliente@correo.com     | cliente123   |

También puedes crear una cuenta de cliente nueva desde la pestaña "Crear cuenta" en la pantalla de inicio.

## Estructura del proyecto

```
imprimex/
├── index.php                  # Registro / inicio de sesión
├── login_process.php
├── register_process.php
├── logout.php
├── config/
│   └── db.php                 # Conexión PDO a MySQL
├── includes/
│   ├── auth.php                # Sesión y control de acceso por rol
│   ├── helpers.php             # Badges de estado, formato de fecha/moneda
│   └── nav_sidebar.php         # Menú lateral según el rol
├── css/
│   └── style.css
├── cliente/
│   ├── panel.php               # Mis pedidos
│   ├── crear_pedido.php        # Formulario nuevo pedido
│   ├── guardar_pedido.php      # Procesa el formulario
│   └── estado_pedido.php       # Seguimiento visual del pedido
├── empleado/
│   ├── panel.php                # Cola de trabajo
│   └── actualizar_estado.php
├── admin/
│   ├── panel.php                # Vista global + asignación de empleados
│   └── asignar_empleado.php
├── uploads/                     # Archivos de diseño subidos por clientes
└── database/
    └── schema.sql               # Esquema completo + datos de prueba
```

## Flujo del sistema
- Un cliente se registra o inicia sesión y ve sus pedidos en `Panel Cliente`.
- Crea un pedido nuevo desde `Crear Pedido`; el sistema calcula un costo estimado según la categoría.
- El administrador ve todos los pedidos en `Panel Administrador` y asigna un empleado.
- El empleado ve su cola de trabajo en `Panel Empleado` y va avanzando el estado del pedido
  (Recibido → Confirmado → En producción → Control de calidad → Listo para entrega → Entregado).
- El cliente y el administrador ven el progreso en tiempo real en `Estado del Pedido`.
