<?php
namespace App\Core;

class ConcurrencyException extends \RuntimeException {
    public function __construct(string $message = 'Conflicto de concurrencia detectado', int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
