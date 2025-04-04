<?php

namespace App\Exceptions;

use CodeIgniter\Debug\BaseExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class ExceptionHandler extends BaseExceptionHandler implements ExceptionHandlerInterface
{
    protected ?string $viewPath = APPPATH . 'Views/exception/';

    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode,
    ): void {
        /** @var \App\Exceptions\HttpException $exception */
        $response->setStatusCode($exception->getStatusCode())->setJSON([
            "status" => "fail",
            "message" => $exception->getMessage(),
        ])->send();
        
        exit($exitCode);
    }
}