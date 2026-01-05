<?php
/**
 * Autoloader Simple para el Sistema de Repuestos
 * Carga automáticamente las clases según el namespace
 */

spl_autoload_register(function ($fqcn) {
    // Solo manejamos namespaces que comienzan con App\ (nuestro espacio de nombres principal)
    $original = $fqcn;
    $prefix = 'App\\';
    if (strpos($fqcn, $prefix) === 0) {
        $fqcn = substr($fqcn, strlen($prefix)); // quitar 'App\'
    }

    // Convertir separadores de namespace a separadores de directorio
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $fqcn) . '.php';
    $fullPath = SRC_PATH . DIRECTORY_SEPARATOR . $relativePath;

    if (file_exists($fullPath)) {
        require_once $fullPath;
        return;
    }

    // Fallback: intentar también sin quitar prefijo (por si se reorganiza estructura)
    $altPath = SRC_PATH . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $original) . '.php';
    if (file_exists($altPath)) {
        require_once $altPath;
        return;
    }

    throw new Exception("Clase no encontrada: {$original}");
});
