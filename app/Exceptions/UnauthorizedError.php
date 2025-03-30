<?php

namespace App\Exceptions;

class UnauthorizedError extends HttpException
{
    public function __construct($message)
    {
        parent::__construct($message, 401);
    }
}