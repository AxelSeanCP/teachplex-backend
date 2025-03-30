<?php

namespace App\Controllers;

use App\Exceptions\HttpException;
use CodeIgniter\RESTful\ResourceController;
use Exception;

class ApiBaseController extends ResourceController
{
    protected $format = "json";

    // protected function response($data, $message = "action successfull", $statusCode = 200)
    // {
    //     return $this->respond([
    //         "status" => "success",
    //         "message" => $message,
    //         "data" => $data
    //     ], $statusCode);
    // }

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
