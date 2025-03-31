<?php

namespace App\Services;

abstract class BaseService
{
    protected $helpers = ['uuid_helper']; //defined in helper folder

    public function __construct()
    {
        helper("uuid_helper");
    }

    protected function generateId($prefix)
    {
        return $prefix . "-" . generateUUID();
    }
}