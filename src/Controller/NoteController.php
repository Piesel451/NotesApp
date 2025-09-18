<?php
namespace App\Controller;

use App\Http\Response;
use App\Entity\Note;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;

class NoteController{
    private NoteRepository $noteRepo;
    private UserRepository $userRepo;

    public function __construct(NoteRepository $noteRepo, UserRepository $userRepo) 
    {
        $this->noteRepo = $noteRepo;
        $this->userRepo = $userRepo;
    }

    //Add further validation such as length, allowed characters, etc.
    //After adding user authentication protect note-related endpoints so only authenticated users can use them.
    //Add note editing and deletion only for authenticated user
    public function addNote()
    {
        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData, true);

        if (!isset($data['title'], $data['content'], $data['user_id'])) {
            Response::jsonResponse([
            "success" => false,
            "error" => "Missing required fields (title, content, user_id)"
            ], 400);
            return;
        }
        $title = htmlspecialchars(trim($data['title']));
        $content = htmlspecialchars(trim($data['content']));

        $user_id = filter_var($data['user_id'], FILTER_VALIDATE_INT);

        if($this->userRepo->userExistsById($user_id))
        {
            try{
            $note = new Note($title, $content, $user_id);
            $this->noteRepo->insertNote($note);
            Response::jsonResponse([
                "success" => true,
                "message" => "Note added successfully"
            ], 201);
            } catch (\PDOException $e) {
            Response::jsonResponse([
                "success" => false,
                "error" => "Failed to add note: " . $e->getMessage()
            ], 500);
            }
        } else {
            Response::jsonResponse([
            "success" => false,
            "error" => "Cannot add note: user with the provided ID does not exist."
            ], 404);
        }
    }
    
}

?>