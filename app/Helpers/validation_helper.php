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

function validateMultipartRequest(string $group, array $messages = [])
{
    $validation = Services::validation();
    $request = service('request');

    $thumbnail = $request->getFile('thumbnail');

    $rules = config("validation")->$group;

    if (! $thumbnail || ! $thumbnail->isValid()) {
        unset($rules['thumbnail']);
    }

    // Let CI handle post + file fields internally via $_POST + $_FILES
    if (! $validation->setRules($rules)->withRequest($request)->run(null, null, $messages)) {
        $errors = $validation->getErrors();
        $firstError = reset($errors);
        throw new BadRequestError($firstError);
    }

    $data = array_merge($request->getPost(), $request->getFiles());

    // log_message('debug', 'Post data: ' . json_encode($data));

    // $thumbnail = $data["thumbnail"];
    // if ($thumbnail && $thumbnail->isValid()) {
    //     log_message('debug', 'Thumbnail name: ' . $thumbnail->getName());
    //     log_message('debug', 'Thumbnail MIME type: ' . $thumbnail->getMimeType());
    //     log_message('debug', 'Thumbnail size: ' . $thumbnail->getSizeByUnit('kb') . ' KB');
    // } else {
    //     log_message('debug', 'Thumbnail not uploaded or invalid.');
    // }

    return $data;
}
