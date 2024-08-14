<?php

namespace Juanpg\Themoviedb\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Juanpg\Themoviedb\Contracts\MovieServiceInterface;
use Juanpg\Themoviedb\Exceptions\GeneralException;
use Juanpg\Themoviedb\Exceptions\MovieServiceException;
use Juanpg\Themoviedb\Http\JsonResponse;
use Juanpg\Themoviedb\Models\Movie;
use Juanpg\Themoviedb\Services\GuzzleMovieService;


/**
 * Controlador encargado de manejar las acciones sobre el Movies
 *
 */
class MoviesController
{
    private MovieServiceInterface $movieService;

    public function __construct()
    {
        // Se debería inyectar el servicio desde fuera para permitir cambios sin afectar al archivo.
        $this->movieService = new GuzzleMovieService(
            apiUrl: $_ENV['THEMOVIEDB_API_URL'],
            bearerToken: $_ENV['THEMOVIEDB_API_BEARER_TOKEN']
        );
    }

    /**
     * Método correspondiente a la acción SHOW en Rest, se encarga de hacer la solicitud al servicio y devolver los datos en formato json
     *
     * @param $movie
     * @return void
     * @throws GuzzleException
     */
    public function show($movie): void
    {
        try {

            $serviceData = $this->movieService->searchByTitle($movie);

            if (!$this->hasMovies($serviceData)) {
                JsonResponse::error('Película no encontrada')->send();
            }

            $movie = Movie::fromServiceData($serviceData);
            $movie->getSimilarMovies(movieService: $this->movieService);

            JsonResponse::success($movie->toArray())->send();

        } catch (GeneralException|MovieServiceException $e) {
            JsonResponse::error($e->getMessage());
        }

    }

    private function hasMovies($data): bool
    {
        return $data['total_results'] > 0;
    }

}
