<?php
    declare(strict_types=1);

    header("Content-Type: application/json");
    require __DIR__  . "/../vendor/autoload.php";
    require __DIR__ . '/../config/bootstrap.php'; 

    use Phroute\Phroute\RouteCollector;
    use Phroute\Phroute\Dispatcher;
    use Phroute\Phroute\Exception\HttpRouteNotFoundException;
    use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
    use App\Config\Database;
    use App\Http\Response;
    use App\Repository\UserRepository;
    use App\Controller\RegisterController;
    use App\Repository\NoteRepository;
    use App\Controller\NoteController;
    use App\Controller\LoginController;

    $pdo = Database::getConnection();
    $userRepo = new UserRepository($pdo);
    $registerController = new RegisterController($userRepo);

    $notesRepo = new NoteRepository($pdo);
    $noteController = new NoteController($notesRepo, $userRepo);

    $loginController = new LoginController($userRepo);

    $path = parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);

    // Remove base path (/NotesApp/public)
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $path = '/' . ltrim(substr($path, strlen($base)), '/');

    $router = new RouteCollector;

    $router->post("/register", [$registerController, "register"]);
    $router->post("/notes", [$noteController, "addNote"]);
    $router->post("/login", [$loginController, "login"]);

    $dispatcher = new Dispatcher($router->getData());

    try{
        $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $path);
    } catch (HttpRouteNotFoundException $e){
        Response::jsonResponse([
            "success" => false,
            "error" => "Route not found"
        ], 404);
    } catch (HttpMethodNotAllowedException $e){
        Response::jsonResponse([
            "success" => false,
            "error" => "Method not allowed (only POST method allowed)",
        ], 405);
    }
    
    //echo $response;

?>