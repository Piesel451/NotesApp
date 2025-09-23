<?php
namespace App\Controller;

use App\Http\Response;
use App\Http\AuthContext;
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

    //Add note editing
    public function addNote()
    {
        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData, true);

        $auth = AuthContext::getUser(); //gets decoded JWT
        $currUserID = $auth->sub;

        if (!isset($data['title'], $data['content'])) {
            Response::jsonResponse([
            "success" => false,
            "error" => "Missing required fields (title, content, user_id)"
            ], 400);
            return;
        }
        $title = trim($data['title']);
        $content = trim($data['content']);
        $currUserID = filter_var($currUserID, FILTER_VALIDATE_INT);

        if (mb_strlen($content, 'UTF-8') > 10000 || mb_strlen($title, 'UTF-8') > 255) {
            Response::jsonResponse([
                "success" => false,
                "error" => "Content or title of the note is too long"
            ], 400);
            return;
        }

        if($this->userRepo->userExistsById($currUserID))
        {
            try{
            $note = new Note($title, $content, $currUserID);
            $insertedNoteID = $this->noteRepo->insertNote($note);
            Response::jsonResponse([
                "success" => true,
                "message" => "Note added successfully",
                "note_id"=> $insertedNoteID
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
            return;
        }
    }

    public function deleteNote()
    {
        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData, true);

        $auth = AuthContext::getUser(); //gets decoded JWT
        $currUserID = $auth->sub;

        if (!isset($data['note_id'])) {
            Response::jsonResponse([
                "success" => false,
                "error" => "Missing note id"
            ],400);
            return;
        }

        $noteId = filter_var($data['note_id'], FILTER_VALIDATE_INT);

        if ($noteId === false) {
            Response::jsonResponse([
                "success" => false,
                "error" => "Invalid note id"
            ], 400);
            return;
        }

        $note = $this->noteRepo->findNoteById($noteId);
        if(!$note){
            Response::jsonResponse([
                "success" => false,
                "error" => "Note not found"
            ], 404);
            return;
        }
        if($note->userId !== $currUserID){
            Response::jsonResponse([
                "success" => false,
                "error" => "Cannot delete someone else's note"
            ], 403);
            return;
        }
        try{
            $this->noteRepo->deleteNote($note);
            Response::jsonResponse([
                "success" => true,
                "message" => "Note deleted successfully"
            ]);
        } catch (\PDOException $e) {
            Response::jsonResponse([
                "success" => false,
                "error" => "Failed to add note: " . $e->getMessage()
            ], 500);
        }

    }
    
}

?>