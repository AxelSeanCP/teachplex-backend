<?php

namespace App\Exceptions;

class NotFoundError extends HttpException
{
    public function __construct($message)
    {
        parent::__construct($message, 404);
    }
}