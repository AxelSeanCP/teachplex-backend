<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Services;
use Exception;

class EnrollmentController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = service("enrollmentService");
    }

    public function store()
    {
        // $userId = $this->request->userId; // throw undefined property error
        $courseId = validateRequest("enroll");
        $userId = Services::userContext()->getUserId();

        $id = $this->service->add($userId, $courseId);

        return $this->respond([
            "status" => "success",
            "message" => "User enrolled",
            "data" => [
                "enrollmentId" => $id
            ]
        ], 201);
    }

    public function index()
    {
        $userId = Services::userContext()->getUserId();
        $enrollments = $this->service->getAll($userId);

        return $this->respond([
            "status" => "success",
            "data" => [
                "enrollments" => $enrollments
            ]
        ]);
    }

    public function destroy()
    {
        $courseId = validateRequest("enroll");
        $userId = Services::userContext()->getUserId();

        $this->service->verifyEnrollmentAccess($userId, $courseId);
        $this->service->delete($userId, $courseId);

        return $this->respond([
            "status" => "success",
            "message" => "Enrollments deleted"
        ]);
    }
}
