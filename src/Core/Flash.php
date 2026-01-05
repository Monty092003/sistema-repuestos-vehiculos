<?php
/**
 * Flash - Helper simple para mensajes de sesión (éxito, error, info)
 * Uso:
 *   Flash::success('Texto');
 *   Flash::error('Texto');
 *   Flash::get('success'); // obtiene y limpia
 */
namespace App\Core;

class Flash {
    const FLASH_KEY = '_flash';

    protected static function ensureSession() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function add($type, $message) {
        self::ensureSession();
        $_SESSION[self::FLASH_KEY][$type] = $message;
    }

    public static function success($message) { self::add('success', $message); }
    public static function error($message) { self::add('error', $message); }
    public static function info($message) { self::add('info', $message); }

    public static function get($type) {
        self::ensureSession();
        if (!isset($_SESSION[self::FLASH_KEY][$type])) return null;
        $msg = $_SESSION[self::FLASH_KEY][$type];
        unset($_SESSION[self::FLASH_KEY][$type]);
        return $msg;
    }

    public static function pullAll() {
        self::ensureSession();
        $all = $_SESSION[self::FLASH_KEY] ?? [];
        unset($_SESSION[self::FLASH_KEY]);
        return $all;
    }
}
