<?php
use Juanpg\Themoviedb\Http\Router;

const BASE_PATH = __DIR__. '/';

require_once BASE_PATH . 'src/helpers.php';

require_once basePath('vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

$router = new Router();

// Registro de la ruta GET /movies/movie
$router->get('/movies/{movie}', 'MoviesController@show');

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method);
