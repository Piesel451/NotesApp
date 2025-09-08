<?php

namespace App\Repository;

use PDO;
use App\Entity\User;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function insertUser(User $user): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (id, email, password_hash, created_at) 
                VALUES ('', :email, :hashedPassword, :createdAt)");
        
        $stmt->execute([
            ":email" => $user->email,
            ":hashedPassword" => $user->passwordHash,
            ":createdAt" => $user->createdAt
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findUserByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$row){
            return null;
        }

        return new User($row['email'], $row['password_hash'], (int)$row['id'], $row['created_at']);
    }

}
?>