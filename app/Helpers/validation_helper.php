<?php

use Config\Services;
use App\Exceptions\BadRequestError;

function validateRequest(string $group, array $messages = [])
{
    $validation = Services::validation();
    $request = service("request");
    $data = $request->getJson(true);

    if (!$validation->run($data, $group)) {
        $errors = $validation->getErrors();

        $firstError = reset($errors);

        throw new BadRequestError($firstError);
    }

    return $data;
}