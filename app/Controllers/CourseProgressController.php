<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Services;

class CourseProgressController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = service("courseProgressService");
    }

    public function show($courseId = null)
    {
        $userId = Services::userContext()->getUserId();

        $progress = $this->service->getCourseProgress($userId, $courseId);

        return $this->respond([
            "status" => "success",
            "data" => [
                "progress" => $progress,
            ]
        ], 200);
    }
}
