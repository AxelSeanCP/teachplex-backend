<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CourseController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = service("courseService");
    }

    public function store()
    {
        $courseData = validateMultipartRequest("course");
        log_message("debug", json_encode($courseData));

        $id = $this->service->add($courseData);

        return $this->respond([
            "status" => "success",
            "data" => [
                "courseId" => $id,
            ],
        ], 201);
    }

    public function index()
    {
        $courses = $this->service->getAll();

        return $this->respond([
            "status" => "success",
            "data" => [
                "courses" => $courses,
            ] 
        ], 200);
    }

    // public function image($filename)
    // {
    //     $filename = basename($filename);

    //     $filePath = WRITEPATH . 'uploads/course_images/' . $filename;
    //     log_message("debug", $filePath);

    //     if (!is_file($filePath)) {
    //         return $this->response->setStatusCode(404)->setJSON([
    //             "status" => "fail",
    //             "message" => "Image not found"
    //         ]);  
    //     }

    //     $mimeType = mime_content_type($filePath);
    //     return $this->response->setHeader("Content-Type", $mimeType)->setBody(file_get_contents($filePath));
    // }
}
