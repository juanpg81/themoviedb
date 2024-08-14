<?php

namespace Juanpg\Themoviedb\Http;

/**
 * Clase que se encarga del enrutar las peticiones recibidas por URL hacia los controladores y métodos correspondientes
 *
 */
class Router
{
    protected array $routes = [];

    /**
     * Método que registra una ruta GET
     *
     * @param $route
     * @param $action
     * @return void
     */
    public function get($route, $action): void
    {
        $this->addRoute($route, $action, 'GET');
    }

    /**
     * Realiza el enrutado de la petición en base al uri y método seleccionado
     *
     * @param $uri
     * @param $method
     * @return void
     */
    public function route($uri, $method): void
    {
        list($selectedControllerAction, $params) = $this->checkRouteMatches($uri, $method);

        if (!$selectedControllerAction) {
            JsonResponse::error('No se encontró la ruta especificada')->send();
        }

        // $params[0] será el primer parámetro dinámico
        $this->callControllerAction($selectedControllerAction, $method, $params[0]);
    }

    /**
     * Registra una nueva ruta
     *
     * @param $ruta
     * @param $action
     * @param $method
     * @return void
     */
    private function addRoute($ruta, $action, $method): void
    {
        $this->routes[] = [
            'uriPattern' => $this->extractRoutePattern($ruta),
            'action' => $action,
            'method' => strtoupper($method)
        ];
    }

    /**
     * Dado una ruta extrae parámetros encerrados entre llaves que se consideraran dinamicos. Creando una
     * expresión regular para poder capturar las rutas concretas
     *
     * @param $route
     * @return string
     */
    private function extractRoutePattern($route): string
    {
        $routePattern = preg_replace('#\{(\w+)}#', '(\w+|\s+)+', $route);
        $routePattern = str_replace('/', '\/', $routePattern);
        return '#^' . $routePattern . '$#'; // Uso # como delimitador de la regexp para mejorar legibilidad
    }

    /**
     * Extrae de una ruta el controlador y métodos a ejecutar pasándole los argumentos que correspondan.
     *
     * @param string $selectedAction
     * @param string $method
     * @param $args
     * @return void
     */
    private function callControllerAction(string $selectedAction, string $method, $args): void
    {
        list($controller, $method) = explode('@', $selectedAction);

        // Asumo que todos los controladores estarán en la misma carpeta
        $controller = "Juanpg\\Themoviedb\\Controllers\\{$controller}";

        if (class_exists($controller)) {
            $controllerInstance = new $controller();
            if (method_exists($controllerInstance, $method)) {
                call_user_func([$controllerInstance, $method], $args);
            }
        }
    }

    /**
     * Determina si hay coincidencias entre un método y url dados y alguna de las rutas registradas.
     *
     * @param $uri
     * @param $method
     * @return array
     */
    private function checkRouteMatches($uri, $method): array
    {
        // Elimino parte de la URI que no necesito y la decodifico
        $processedUri = preg_replace('#/(\w+)?(/?index\.php)#', '', $uri);
        $processedUriDecoded = urldecode($processedUri);

        $selectedControllerAction = null;
        $params = [];

        foreach ($this->routes as $route) {
            if (preg_match_all($route['uriPattern'], $processedUriDecoded, $matches) && $route['method'] === $method) {
                $selectedControllerAction = $route['action'];
                $numMatches = count($matches);
                // Asumo que solo se recibirá un parámetro dinámico para simplificar
                $params[] = $numMatches > 1 ? $matches[1][0] : null;
                break;
            }
        }
        return [$selectedControllerAction, $params];
    }
}
