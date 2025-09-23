<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Http\Response;

use Firebase\JWT\JWT;

class LoginController
{
    private UserRepository $repo;

    public function __construct(UserRepository $repo) 
    {
        $this->repo = $repo;
    }

    public function login()
    {
        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData, true);

        if (!isset($data['email'], $data['password'])){
            Response::jsonResponse([
                "success" => false,
                "error" => "Missing required fields (email, password)"
            ], 400);
            return;
        }

        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $password = $data['password'];

        $user = $this->repo->findUserByEmail($email);
        $authenticated = password_verify($password, $user->passwordHash);
        if (!$user || !$authenticated) {
            Response::jsonResponse([
                "success" => false,
                "error" => "Invalid email or password"
            ], 401);
            return;
        }
        //Generate and return JWT for logged user
        $payload = [
            "iss" => $_ENV['JWT_ISSUER'],
            "aud" => $_ENV['JWT_AUDIENCE'],
            "iat" => time(),
            "nbf" => time(),
            "exp" => time() + (int)$_ENV['JWT_EXP'],
            "sub" => $user->id,
            "email" => $user->email,
        ];

        $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        Response::jsonResponse([
            "success" => true,
            "token" => $jwt,
            "user" => [
                "id" => $user->id,
                "email" => $user->email
            ]
        ]);

    }

}