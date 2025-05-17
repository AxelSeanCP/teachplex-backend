<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class LessonController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = service("lessonService");
    }

    public function store($courseId = null)
    {
        // change to validateMultipartRequest when implementing video url, code text
        // if so make sure to use multipart form-data on frontend
        $lessonData = validateRequest("lesson");

        $id = $this->service->add($courseId, $lessonData);

        return $this->respond([
            "status" => "success",
            "data" => [
                "lessonId" => $id
            ]
        ], 201);
    }

    public function show($courseId = null, $lessonId = null)
    {
        $lesson = $this->service->get($lessonId);

        return $this->respond([
            "status" => "success",
            "data" => [
                "lesson" => $lesson,
            ]
        ], 200);
    }

    public function update($courseId = null, $lessonId = null)
    {
        // change to validateMultipartRequest when implementing video url, code text
        // if so make sure to use multipart form-data on frontend
        $lessonData = validateRequest("lesson");

        $this->service->edit($lessonId, $lessonData);

        return $this->respond([
            "status" => "success",
            "message" => "Lesson updated successfully",
        ], 200);
    }

    public function destroy($courseId = null, $lessonId = null)
    {
        $this->service->remove($lessonId);

        return $this->respond([
            "status" => "success",
            "message" => "Lesson deleted successfully"
        ], 200);
    }
}
