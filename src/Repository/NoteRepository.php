<?php
namespace App\Repository;

use PDO;
use App\Entity\Note;

class NoteRepository
{
    private PDO $pdo;
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function insertNote(Note $note): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO notes (id, user_id, title, content, created_at) VALUES ('', :user_id, :title, :content, :created_at)"
        );
        $stmt->execute([
            ':user_id' => $note->userId,
            ':title' => $note->title,
            ':content' => $note->content,
            ':created_at' => $note->createdAt
        ]);
        return (int)$this->pdo->lastInsertId();
    }
}

?>