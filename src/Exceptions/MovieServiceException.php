<?php

namespace Juanpg\Themoviedb\Exceptions;

class MovieServiceException extends \Exception
{

    public function __construct(protected $message = "", protected $code = 0)
    {
        parent::__construct($message, $code);
    }

}
