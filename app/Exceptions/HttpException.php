<?php

namespace App\Exceptions;

use Exception;

class HttpException extends Exception
{
    protected $message = "An error occured";
    protected $statusCode = 400;

    public function __construct($message, $statusCode = 400)
    {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}