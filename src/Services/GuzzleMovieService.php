<?php

namespace Juanpg\Themoviedb\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Juanpg\Themoviedb\Contracts\MovieServiceInterface;
use Juanpg\Themoviedb\Exceptions\GeneralException;
use Juanpg\Themoviedb\Exceptions\MovieServiceException;

/**
 * Implementación concreta del interfaz MovieServiceInterface utilizando el paquete GuzzleHttp
 */
class GuzzleMovieService implements MovieServiceInterface
{
    private Client $client;

    public function __construct(private readonly string $apiUrl, private readonly string $bearerToken)
    {
        $this->client = new Client([
            'base_uri' => $this->apiUrl
        ]);
    }

    /**
     * Realiza una petición a API externo solicitando datos de una película dado su titulo
     *
     * @param string $title - título a buscar
     * @param bool $includeAdult - Determina si se incluye contenido para adultos
     * @param string $language - Indica el idioma que se ha de utilizar
     * @return array - datos devueltos por el servicio
     * @throws GeneralException
     * @throws MovieServiceException
     * @throws GuzzleException
     */
    public function searchByTitle(string $title, bool $includeAdult = false, string $language = 'es-ES'): array
    {
        try {
            $response = $this->client->request('GET', "search/movie", [
                'query' => [
                    'query' => $title,
                    'include_adult' => $includeAdult,
                    'language' => $language,
                    'page' => 1
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->bearerToken,
                    'accept' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new MovieServiceException("Error en la solicitud: " . $response->getStatusCode());
            }

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            throw new MovieServiceException(message: 'Error al conectar con el servicio externo.' . $e->getMessage(), code: $e->getCode());
        } catch (Exception $e) {
            throw new GeneralException(message: 'Ocurrió un error inesperado: ' . $e->getMessage(), code: $e->getCode());
        }

    }


    /**
     * Realiza una petición a API externo solicitando películas similares dado un id de película
     *
     * @param int $movieId - Id de la película sobre la que se quieren solicitar similares
     * @param string $language - Indica el idioma que se ha de utilizar
     * @return array - datos devueltos por el servicio
     * @throws GeneralException
     * @throws GuzzleException
     * @throws MovieServiceException
     */
    public function searchSimilarById(int $movieId, string $language = 'es-ES'): array
    {
        try {
            $response = $this->client->request('GET', "movie/{$movieId}/similar", [
                'query' => [
                    'language' => $language,
                    'page' => 1
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->bearerToken,
                    'accept' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new MovieServiceException("Error en la solicitud: " . $response->getStatusCode());
            }

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            throw new MovieServiceException(message: 'Error al conectar con el servicio externo.' . $e->getMessage(), code: $e->getCode());
        } catch (Exception $e) {
            throw new GeneralException(message: 'Ocurrió un error inesperado: ' . $e->getMessage(), code: $e->getCode());
        }

    }

}
