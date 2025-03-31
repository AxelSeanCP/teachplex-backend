<?php

namespace App\Exceptions;

class BadRequestError extends HttpException
{
    public function __construct($message)
    {
        parent::__construct($message, 400);
    }
}