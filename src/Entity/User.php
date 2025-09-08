<?php

namespace App\Entity;

class User
{
    public $email;
    public $passwordHash;
    public $id;
    public $cratedAt;

    public function __construct(string $email, string $passwordHash, ?int $id = null, ?string $cratedAt = null)
    {
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->id = $id;
        $this->createdAt = $createdAt ?? date('Y-m-d H:i:s');;
    }


}

