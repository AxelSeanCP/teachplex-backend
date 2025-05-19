<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Services;

class CourseController extends BaseController
{
    protected $service;
    protected $lessonService;

    public function __construct()
    {
        $this->service = service("courseService");
        $this->lessonService = service("lessonService");
    }

    public function store()
    {
        // make sure to use multipart/form-data in frontend
        $courseData = validateMultipartRequest("course");

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

    public function show($id = null)
    {
        $course = $this->service->get($id);

        return $this->respond([
            "status" => "success",
            "data" => [
                "course" => $course,
            ]
        ], 200);
    }

    public function update($id = null)
    {
        $courseData = validateMultipartRequest("course");

        $this->service->edit($id, $courseData);

        return $this->respond([
            "status" => "success",
            "message" => "Course updated successfully"
        ], 200);
    }

    public function destroy($id = null)
    {
        $this->service->remove($id);

        return $this->respond([
            "status" => "success",
            "message" => "Course deleted successfully"
        ], 200);
    }

    public function finish($id = null)
    {
        $userId = Services::userContext()->getUserId();

        // check if all lesson is complete under this courseId and userId
        $this->lessonService->checkAllLessonComplete($userId, $id);
        $this->service->completeCourse($userId, $id);

        return $this->respond([
            "status" => "success",
            "message" => "Course completed",
        ], 200);
    }
}
