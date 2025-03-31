<?php

namespace App\Controllers;

use App\Exceptions\HttpException;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;

class BaseController extends ResourceController
{
    use ResponseTrait;

    protected $format = "json";
    protected $helpers = ["validation_helper"];

    protected function error($message = "An error occured", $statusCode = 400)
    {
        return $this->respond([
            "status" => "fail",
            "message" => $message
        ], $statusCode);
    }

    protected function handleException(Exception $e)
    {
        if ($e instanceof HttpException) {
            return $this->respond([
                "status" => "fail",
                "message" => $e->getMessage()
            ], $e->getStatusCode());
        }

        log_message("error", $e);

        return $this->respond([
            "status" => "error",
            "message" => "Internal Server Error",
            "error" => ENVIRONMENT === "development" ? $e->getMessage() : null
        ], 500);
    }
}
