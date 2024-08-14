# API REST Themoviedb

Servicio REST que mostrará información sobre una película dado su título así como máximo 5 películas similares, devolviendo el resultado en formato json.

## Clonado del repositorio
Una vez clonado el repositorio acceder al directorio del proyecto e instalar dependencias usando composer. 

``` composer install ``` 

## Instalación de dependencias
Renombrar el archivo .env.example a .env y añadir las credenciales the themoviedb.

```php 
THEMOVIEDB_API_URL='http://api.themoviedb.org/3/'
THEMOVIEDB_API_KEY=''
THEMOVIEDB_API_BEARER_TOKEN=''

SIMILAR_MOVIES_NUMBER=5
```

## Ejecución de la ruta GET /movies/movie
Para no depender de configuraciones concretas de apache o nginx se procesa el index.php desde la raíz del proyecto y la ruta a ejecutar será como sigue: 

```php
http://localhost/nombreproyecto/index.php/movies/casablanca
```
Siendo nombreproyecto el nombre del directorio con el que se haya clonado el repositorio.
