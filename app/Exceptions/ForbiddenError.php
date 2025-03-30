<?php

namespace App\Exceptions;

class ForbiddenError extends HttpException
{
    public function __construct($message)
    {
        parent::__construct($message, 403);
    }
}