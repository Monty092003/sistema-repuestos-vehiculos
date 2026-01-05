<?php
/**
 * Constantes de la Aplicación
 * Sistema de Repuestos de Vehículos
 */

// Configuración de zona horaria
date_default_timezone_set('America/Lima');

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Repuestos de Vehículos');
define('APP_VERSION', '1.0.0');
define('APP_DEBUG', false); // Configurado para producción

// Rutas de la aplicación
// Ajustar BASE_URL según el entorno: para servidor embebido php -S localhost:8000 usar esta:
define('BASE_URL', 'https://server.gmacservice.com/miapp/public/');
define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_PATH', APP_ROOT . '/public');
define('SRC_PATH', APP_ROOT . '/src');
define('CONFIG_PATH', APP_ROOT . '/config');
define('STORAGE_PATH', APP_ROOT . '/storage');

// Configuración de sesiones
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos

// Roles de usuario
define('ROLE_ADMIN', 'administrador');
define('ROLE_EMPLOYEE', 'empleado');

// Estados de stock
define('STOCK_BAJO_LIMIT', 10); // Límite para alerta de stock bajo
define('STOCK_CRITICO_LIMIT', 5); // Límite para stock crítico
// Factores avanzados de análisis de stock (RF16)
define('NEAR_STOCK_FACTOR', 1.2); // Hasta 120% del mínimo se considera "casi bajo"
define('TARGET_REPLENISH_FACTOR', 1.5); // Objetivo sugerido de reposición (150% del mínimo)

// Categorías de repuestos (pueden expandirse)
define('CATEGORIAS_REPUESTOS', [
    'frenos' => 'Frenos',
    'motor' => 'Motor',
    'suspension' => 'Suspensión',
    'transmision' => 'Transmisión',
    'electrico' => 'Sistema Eléctrico',
    'carroceria' => 'Carrocería',
    'filtros' => 'Filtros',
    'lubricantes' => 'Lubricantes'
]);

// Estados de venta (normalizados en mayúsculas en el modelo)
define('VENTA_PENDIENTE', 'PENDIENTE');
define('VENTA_COMPLETADA', 'COMPLETADA');
define('VENTA_ANULADA', 'ANULADA');

// Tipos de movimiento de inventario
define('MOVIMIENTO_ENTRADA', 'entrada');
define('MOVIMIENTO_SALIDA', 'salida');
define('MOVIMIENTO_AJUSTE', 'ajuste');

// Configuración de paginación
define('ITEMS_POR_PAGINA', 20);

// Configuración de archivos
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Mensajes del sistema
define('MSG_SUCCESS', 'Operación realizada correctamente');
define('MSG_ERROR', 'Ha ocurrido un error');
define('MSG_NOT_FOUND', 'Recurso no encontrado');
define('MSG_UNAUTHORIZED', 'No tiene permisos para realizar esta acción');
define('MSG_LOGIN_REQUIRED', 'Debe iniciar sesión para continuar');
