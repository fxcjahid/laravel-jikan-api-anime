<?php

namespace App\Exceptions;

use Exception;

class JikanApiException extends Exception
{
    protected $statusCode;

    public function __construct($message = "", $statusCode = 500, $previous = null)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, 0, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
