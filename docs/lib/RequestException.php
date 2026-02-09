<?php
namespace TnFAT\Planner;

class RequestException extends \Exception {
    public function __construct(string $message, int $statusCode, ?Throwable $previous = null) {
        parent::__construct($message, $statusCode, $previous);
    }
}