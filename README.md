# 🚗 Sistema de Repuestos de Vehículos

Sistema completo de gestión de inventario de repuestos de vehículos desarrollado con PHP puro, implementando una arquitectura en capas (MVC + Repository Pattern + Service Layer).

## 📋 Características Principales

- ✅ **Gestión de Usuarios** - Sistema de autenticación con roles (Administrador/Empleado)
- ✅ **Gestión de Repuestos** - CRUD completo con categorías y control de stock
- ✅ **Control de Inventario** - Entradas, salidas y ajustes de inventario
- ✅ **Gestión de Proveedores** - Administración de proveedores y historial de compras
- ✅ **Módulo de Ventas** - Registro de ventas con control de stock en tiempo real
- ✅ **Reportes y Dashboard** - Estadísticas y alertas de stock bajo
- ✅ **Seguridad** - Protección CSRF, control de concurrencia y sesiones seguras

## 🏗️ Arquitectura

El proyecto implementa una **arquitectura en capas** siguiendo las mejores prácticas:

```
┌─────────────────────────────────┐
│   CAPA DE PRESENTACIÓN          │ ← Controllers + Views + Router
├─────────────────────────────────┤
│   CAPA DE NEGOCIO               │ ← Services (Lógica de negocio)
├─────────────────────────────────┤
│   CAPA DE ACCESO A DATOS        │ ← Repositories (SQL)
├─────────────────────────────────┤
│   CAPA DE DOMINIO               │ ← Models (Entidades)
├─────────────────────────────────┤
│   CORE                          │ ← Database, Router, CSRF, Flash
└─────────────────────────────────┘
```

### Patrones de Diseño Implementados

- **MVC (Model-View-Controller)** - Separación de responsabilidades
- **Repository Pattern** - Abstracción de acceso a datos
- **Service Layer** - Lógica de negocio centralizada
- **Singleton** - Gestión única de conexión a base de datos
- **Dependency Injection** - Inyección manual de dependencias

## 🛠️ Tecnologías

- **PHP 7.4+** - Lenguaje de programación
- **MySQL/MariaDB** - Base de datos
- **Bootstrap 5** - Framework CSS
- **PDO** - Capa de abstracción de base de datos
- **Git** - Control de versiones

## 📁 Estructura del Proyecto

```
miapp/
├── config/
│   ├── constants.php           # Constantes globales
│   ├── database.php           # Configuración de BD
│   ├── database_schema.sql   # Esquema de base de datos
│   └── env.example           # Ejemplo de variables de entorno
├── public/
│   ├── assets/               # Recursos estáticos
│   └── index.php            # Punto de entrada único
├── src/
│   ├── Controllers/         # Controladores
│   ├── Core/               # Clases fundamentales
│   │   ├── Database.php    # Conexión BD (Singleton)
│   │   ├── Router.php      # Enrutador
│   │   ├── Csrf.php        # Protección CSRF
│   │   └── Flash.php       # Mensajes flash
│   ├── Models/             # Entidades de dominio
│   ├── Repositories/       # Acceso a datos
│   ├── Services/           # Lógica de negocio
│   ├── Views/              # Vistas
│   └── autoloader.php      # Autoloader PSR-4
├── .gitignore
└── README.md
```

## 🚀 Instalación

### Requisitos Previos

- PHP 7.4 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Servidor web (Apache/Nginx) o PHP built-in server

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/Monty092003/sistema-repuestos-vehiculos.git
cd sistema-repuestos-vehiculos
```

2. **Configurar la base de datos**
```bash
# Crear base de datos
mysql -u root -p
CREATE DATABASE server_repuestos_vehiculos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Importar esquema
mysql -u root -p server_repuestos_vehiculos < config/database_schema.sql
```

3. **Configurar credenciales de BD**

Edita `src/Core/Database.php` y actualiza las credenciales:
```php
private $host = 'localhost';
private $dbname = 'server_repuestos_vehiculos';
private $username = 'tu_usuario';
private $password = 'tu_contraseña';
```

4. **Iniciar el servidor**

**Opción 1: Servidor PHP integrado**
```bash
cd public
php -S localhost:8000
```

**Opción 2: Apache/Nginx**
- Configura el DocumentRoot hacia la carpeta `public/`

5. **Acceder al sistema**
```
URL: http://localhost:8000
Usuario por defecto: admin@sistema.com
Contraseña: Admin2025!
```

## 📖 Uso del Sistema

### Credenciales Iniciales

El sistema crea automáticamente usuarios de prueba:

| Rol | Email | Contraseña |
|-----|-------|------------|
| Administrador | admin@sistema.com | Admin2025! |
| Empleado | empleado@sistema.com | Emp2025! |

### Módulos Principales

#### 1. **Dashboard**
- Vista general del sistema
- Estadísticas de inventario
- Alertas de stock bajo
- Movimientos recientes

#### 2. **Gestión de Repuestos**
- Crear, editar y eliminar repuestos
- Asignación de categorías
- Control de precios de compra/venta
- Definición de stock mínimo/máximo
- Búsqueda y filtros

#### 3. **Inventario**
- **Entradas**: Registrar compras a proveedores
- **Salidas**: Registrar salidas de inventario
- **Ajustes**: Corregir discrepancias de stock
- Historial completo de movimientos

#### 4. **Proveedores**
- Gestión de datos de proveedores
- Historial de compras por proveedor
- Estadísticas de proveedores

#### 5. **Ventas**
- Registro de ventas con múltiples items
- Cálculo automático de totales
- Descuentos aplicables
- Actualización automática de stock
- Anulación de ventas (con reversión de stock)

#### 6. **Usuarios**
- Gestión de usuarios del sistema
- Asignación de roles y permisos
- Cambio de contraseñas

## 🔒 Características de Seguridad

- **Protección CSRF** - Tokens en todos los formularios
- **Control de Concurrencia** - Bloqueo pesimista en operaciones críticas
- **Sesiones Seguras** - Regeneración de ID de sesión
- **Control de Permisos** - Sistema de roles y permisos
- **Validación de Datos** - Validación en capas (Frontend + Backend)
- **Prepared Statements** - Protección contra SQL Injection

## 🧪 Control de Concurrencia

El sistema implementa **locking pesimista** para prevenir condiciones de carrera en:
- Ventas simultáneas
- Movimientos de inventario
- Actualización de stock

Ejemplo de manejo:
```php
try {
    $db->beginTransaction();
    $repuestos = $repository->lockMultiple($ids);
    // ... operaciones críticas
    $db->commit();
} catch (ConcurrencyException $e) {
    $db->rollback();
    // Manejo del error de concurrencia
}
```

## 📊 Base de Datos

El sistema utiliza las siguientes tablas principales:

- `usuarios` - Usuarios del sistema
- `repuestos` - Catálogo de repuestos
- `categorias` - Categorías de repuestos
- `proveedores` - Proveedores
- `movimientos_inventario` - Historial de movimientos
- `ventas` - Registro de ventas
- `venta_detalles` - Detalles de cada venta

## 🤝 Contribución

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

## 👨‍💻 Autor

**Alexander Suiza León**
- GitHub: [@Monty092003](https://github.com/Monty092003)
- Email: leonisuizalalexander@gmail.com

## 📞 Soporte

Si encuentras algún bug o tienes sugerencias:
- Abre un [Issue](https://github.com/Monty092003/sistema-repuestos-vehiculos/issues)
- Envía un Pull Request con mejoras

---

⭐ Si te gusta este proyecto, no olvides darle una estrella en GitHub!
