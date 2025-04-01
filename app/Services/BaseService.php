<?php

namespace App\Services;

abstract class BaseService
{
    protected $helpers = ['uuid_helper']; //defined in helper folder

    protected function generateId($prefix)
    {
        return $prefix . "-" . generateUUID();
    }
}