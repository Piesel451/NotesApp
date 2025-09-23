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
    use App\Http\AuthContext;
    use App\Http\Middleware\AuthMiddleware;
    use App\Repository\UserRepository;
    use App\Repository\NoteRepository;
    use App\Controller\RegisterController;
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

    $router->filter('auth',function(){
        $decoded = AuthMiddleware::verify();
        if ($decoded === null) return false;
        AuthContext::setUser($decoded);
    });

    $router->post("/register", [$registerController, "register"]);
    $router->post("/login", [$loginController, "login"]);

    //Routes protected by AuthMiddleware
    $router->group(['before' => 'auth'], function($router) use ($noteController) {
        $router->post("/add-note", [$noteController, "addNote"]);
        $router->post("/edit-note", [$noteController, "editNote"]);
        $router->post("/delete-note", [$noteController, "deleteNote"]);
    });

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

?>