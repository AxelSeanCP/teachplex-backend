<?php

namespace App\Services;

use App\Exceptions\BadRequestError;
use App\Exceptions\ForbiddenError;
use App\Exceptions\NotFoundError;
use App\Models\Enrollment;

class EnrollmentService extends BaseService
{
    protected $model;

    public function __construct(Enrollment $enrollmentModel)
    {
        $this->model = $enrollmentModel;
    }

    public function verifyEnrollment($userId, $courseId) 
    {
        $enrollment = $this->model
        ->where("user_id", $userId)
        ->where("course_id", $courseId)
        ->first();
        
        if ($enrollment) {
            throw new BadRequestError("User already enrolled");
        }
    }

    public function add($userId, $courseId)
    {
        $this->verifyEnrollment($userId, $courseId);

        $id = $this->generateId("enrollment");

        $data = [
            "id" => $id,
            "user_id" => $userId,
            "course_id" => $courseId,
        ];

        $this->model->insert($data);

        return $id;
    }

    public function get($userId, $courseId)
    {
        $enrollment = $this->model->where("user_id", $userId)->where("course_id", $courseId)->first();

        if (!$enrollment) {
            throw new NotFoundError("User is not enrolled to this course");
        }

        return $enrollment;
    }

    public function getMultiple($userId)
    {
        $enrollments = $this->model->where("user_id", $userId)->findAll();

        if (empty($enrollments)) {
            return [];
        }

        return $enrollments;
    }

    public function getAll($name = null)
    {
        $builder = $this->model->select("enrollments.*, users.name as user_name, courses.title as course_title")
        ->join("users", "users.id = enrollments.user_id")
        ->join("courses", "courses.id = enrollments.course_id");

        if ($name) {
            $builder->like("users.name", $name);
        }

        $enrollments = $builder->findAll();

        if (empty($enrollments)) {
            return [];
        }

        return $enrollments;
    }

    // public function edit($userId, $courseId)
    // {
    //     $enrollment = $this->get($userId, $courseId);

    //     $data = [
    //         "isCompleted" => 1
    //     ];

    //     $this->model->update($enrollment["id"], $data);
    // }

    public function delete($id)
    {
        $this->model->delete($id);
    }
}