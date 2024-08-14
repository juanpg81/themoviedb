<?php

namespace Juanpg\Themoviedb\Http;

class JsonResponse
{

    public function __construct(
        protected array  $data,
        protected ?array $headers = null,
        protected ?int   $statusCode = 200,
    )
    {
        $this->headers = array_merge([
            'Content-Type' => 'application/json'
        ], $headers ?? []);
    }

    /**
     * Método estático que crea una respuesta json exitosa
     * @param array $data
     * @param array|null $headers
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function success(array $data, array $headers = null, int $statusCode = 200): JsonResponse
    {
        return new self(data: $data, headers: $headers, statusCode: $statusCode);
    }

    /**
     * Método estático que crea una respuesta de error en json
     * @param string $errorMessage
     * @param array|null $headers
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function error(string $errorMessage, array $headers = null, int $statusCode = 400): JsonResponse
    {
        return new self(data: ['message' => $errorMessage], headers: $headers, statusCode: $statusCode);
    }

    /**
     * Método que establece el status code de la respuesta, sus cabeceras y expone el resultado
     *
     * @return void
     */
    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $header => $value) {
            header("{$header}: {$value}");
        }

        echo json_encode($this->data);
        exit; // corta la ejecución
    }

}
