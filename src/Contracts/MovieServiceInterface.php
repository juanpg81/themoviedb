<?php

namespace Juanpg\Themoviedb\Contracts;

/**
 * Interfaz que define los métodos a implementar para manejar el MovieService
 *
 */
interface MovieServiceInterface
{
    /**
     * Método que realiza una búsqueda de una película dado su titulo
     *
     * @param string $title
     * @return array
     */
    public function searchByTitle(string $title): array;

    /**
     * Método que consulta películas similares dado un id de película
     *
     * @param int $movieId
     * @return array
     */
    public function searchSimilarById(int $movieId): array;
}
