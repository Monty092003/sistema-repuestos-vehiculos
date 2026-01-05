<?php
namespace App\Core;

class Csrf {
    const SESSION_KEY = '_csrf_tokens';
    const FORM_FIELD = '_token';
    const TOKEN_TTL = 3600; // 1 hora

    public static function generate() : string {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
        // Limpiar expirados
        self::prune();
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::SESSION_KEY][$token] = time() + self::TOKEN_TTL;
        return $token;
    }

    public static function field() : string {
        $t = self::generate();
        return '<input type="hidden" name="' . self::FORM_FIELD . '" value="' . htmlspecialchars($t, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validateFromRequest(): bool {
        $token = $_POST[self::FORM_FIELD] ?? '';
        return self::validate($token, true);
    }

    public static function validate(string $token, bool $consume = true): bool {
        if (empty($token)) return false;
        if (!isset($_SESSION[self::SESSION_KEY][$token])) return false;
        $exp = $_SESSION[self::SESSION_KEY][$token];
        if ($exp < time()) {
            unset($_SESSION[self::SESSION_KEY][$token]);
            return false;
        }
        if ($consume) unset($_SESSION[self::SESSION_KEY][$token]);
        return true;
    }

    private static function prune(): void {
        if (!isset($_SESSION[self::SESSION_KEY])) return;
        $now = time();
        foreach ($_SESSION[self::SESSION_KEY] as $tok => $exp) {
            if ($exp < $now) unset($_SESSION[self::SESSION_KEY][$tok]);
        }
    }
}
