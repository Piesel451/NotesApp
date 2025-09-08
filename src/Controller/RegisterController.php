<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Http\Response;

class RegisterController
{
    private UserRepository $repo;

    public function __construct(UserRepository $repo) 
    {
        $this->repo = $repo;
    }

    public function register(): string
    {
        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData, true);

        if (!isset($data['email'], $data['password'])){
            Response::jsonResponse([
                "success" => false,
                "error" => "Missing required fields (email, password)"
            ], 400);
            exit;
        }

        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $password = $data['password'];

        if($this->repo->findUserByEmail($email)){
                Response::jsonResponse([
                "success" => false,
                "error" => "User already exists"
            ], 409);
            exit;
        }

        try {
            $user = new User($email, password_hash($password, PASSWORD_DEFAULT));
            $userId = $this->repo->insertUser($user);

            Response::jsonResponse([
                "success" => true,
                "user_id" => $userId,
                "email" => $email
            ]);
        } catch (PDOException $e) {
            Response::jsonResponse([
                "success" => false,
                "error" => "Failed to create user". $e->getMessage()
            ], 500);
        }
    }
}

?>