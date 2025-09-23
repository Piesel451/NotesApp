<?php
namespace App\Http\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Http\Response;

class AuthMiddleware {
    public static function verify() {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? '';

        if (!preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
            return Response::jsonResponse([
                "success" => false,
                'message' => 'Missing token'
            ], 401);
        }

        $token = $matches[1];

        // TODO Configure Redis to blacklist(revoke) tokens
        // $redis = new \Predis\Client([
        //     'host' => $_ENV['REDIS_HOST'],
        //     'port' => $_ENV['REDIS_PORT']
        // ]);

        // if ($redis->get("blacklist:$token")) {
        //     Response::jsonResponse([
        //         "success" => false,
        //         "error" => "Token revoked"
        //     ], 401);
        //     return;
        // }

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            Response::jsonResponse([
                "success" => false,
                "error" => "Invalid token"
            ], 401);
            return;
        }
    }  
}