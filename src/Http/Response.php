<?php

namespace App\Http;

class Response {
    public static function jsonResponse(array $data, int $status = 200): void {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}