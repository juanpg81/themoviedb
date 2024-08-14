<?php

namespace Juanpg\Themoviedb\Models;

use Juanpg\Themoviedb\Contracts\MovieServiceInterface;

/**
 * Clase que representara a cada instancia de Movie
 *
 */
class Movie
{
    protected int $id;
    protected string $title;
    protected string $originalTitle;
    protected float $voteAverage;
    protected string $releaseDate;
    protected string $overview;
    protected array $similar;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->title = $data['title'];
        $this->originalTitle = $data['original_title'];
        $this->voteAverage = $data['vote_average'];
        $this->releaseDate = $data['release_date'];
        $this->overview = $data['overview'];
        $this->similar = $data['similar'] ?? [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOriginalTitle(): string
    {
        return $this->originalTitle;
    }

    public function getAverageScore(): float
    {
        return $this->voteAverage;
    }

    public function getReleaseDate(): string
    {
        return $this->releaseDate;
    }

    public function getOverview(): string
    {
        return $this->overview;
    }

    public function getSimilar(): array
    {
        return $this->similar;
    }

    /**
     * Llama a $movieService solicitando peliculas similares a la de la instancia actual
     *
     * @param MovieServiceInterface $movieService
     * @return array
     */
    public function getSimilarMovies(MovieServiceInterface $movieService): array
    {
        $serviceData = $movieService->searchSimilarById($this->getId());
        $numOfSimilar = min([$_ENV['SIMILAR_MOVIES_NUMBER'], count($serviceData['results'])]);

        for ($i = 0; $i < $numOfSimilar; $i++) {
            $similarMovie = $serviceData['results'][$i];

            $title = $similarMovie['title'];
            $release_year = $this->getYearFromStringDate($similarMovie['release_date']);

            $this->similar[] = "{$title} ({$release_year})";
        }

        return $this->similar;
    }

    /**
     * Método de clase que recibe un array de datos desde el servicio y devuelve una instancia del modelo
     *
     * @param array $data
     * @return Movie
     */
    public static function fromServiceData(array $data): Movie
    {
        // $data['results'][0] es la primera película del listado
        return new self(data: $data['results'][0]);
    }

    /**
     * Devuelve la instancia en formato array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'original_title' => $this->getOriginalTitle(),
            'average_score' => $this->getAverageScore(),
            'release_date' => $this->getReleaseDate(),
            'overview' => $this->getOverview(),
            'similar' => $this->getSimilar()
        ];
    }

    /**
     * Devuelve la instancia en formato json o false si hubiera algún error
     *
     * @return false|string
     */
    public function toJson(): false|string
    {
        return json_encode($this->toArray());
    }

    /**
     * Extrae el año de una fecha en formato  'YYYY-MM-DD'
     *
     * @param string $stringDate
     * @return string
     */
    private function getYearFromStringDate(string $stringDate): string
    {
        return explode('-', $stringDate)[0];
    }

}
