# EasyTickets — Gestión de incidencias

Actividad integradora 3 · Aplicación web con PHP, MySQL y patrón MVC
Universidad ECOTEC · Ingeniería en Sistemas Inteligentes

## Qué hace
Registra incidencias de soporte técnico y las muestra en un tablero organizado por
estado. Las tarjetas se arrastran de una columna a otra y el nuevo estado se guarda
en MySQL.

Estados del tablero: **Nuevo → En proceso → Resuelto → Cerrado**

## Flujo de la aplicación
```
Navegador → index.php → Controlador → Modelo → MySQL
                            ↓
                          Vista
```

Ninguna vista consulta la base de datos y ningún modelo imprime HTML.

## Estructura

```
actividad-integradora-3/
├── index.php                        Punto de entrada y enrutador
├── config/
│   └── conexion.php                 Credenciales y objeto PDO
├── controllers/
│   └── IncidenciaController.php     Recibe acciones y coordina
├── models/
│   └── Incidencia.php               Consultas SQL
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── incidencias/
│   │   ├── crear.php                Formulario de registro
│   │   ├── listar.php               Tabla de consulta
│   │   └── tablero.php              Tablero por estado
│   └── 404.php
├── css/
│   └── estilos.css
├── js/
│   ├── validaciones.js              Validaciones del formulario
│   └── tablero.js                   Arrastrar y soltar tarjetas
└── database/
    └── integradora.sql              Script de la base de datos
```

## Base de datos

Nombre: `integradora`. Dos tablas: `categorias` (catálogo) e `incidencias` (principal).

## Instalación local

1. Copiar la carpeta dentro de `C:\xampp\htdocs\`.
2. Iniciar Apache y MySQL desde el panel de XAMPP.
3. Importar `database/integradora.sql` desde phpMyAdmin.
4. Abrir `http://localhost/actividad-integradora-3/`.

Usuario de MySQL: `root`, sin contraseña.
