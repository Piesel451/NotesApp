<?php
namespace App\Entity;

class Note
{
    public $id;
    public $title;
    public $content;
    public $createdAt;
    public $userId;

    public function __construct(string $title, string $content, int $userId, ?int $id = null, ?string $createdAt = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->userId = $userId;
        $this->id = $id;
        $this->createdAt = $createdAt ?? date('Y-m-d H:i:s');
    }
    
}
?>