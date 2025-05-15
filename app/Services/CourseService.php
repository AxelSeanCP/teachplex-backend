<?php

namespace App\Services;

use App\Exceptions\BadRequestError;
use App\Exceptions\NotFoundError;
use App\Models\Course;

class CourseService extends BaseService
{
    protected $model;

    public function __construct(Course $courseModel)
    {
        $this->model = $courseModel;
    }

    public function verifyCourse($title)
    {
        $course = $this->model->where("title", $title)->first();

        if ($course) {
            throw new BadRequestError("Course already exists");
        }
    }

    public function uploadThumbnail($thumbnail = null) 
    {
        $uploadPath = FCPATH . 'images/course_images/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if (!$thumbnail || !$thumbnail->isValid() || $thumbnail->hasMoved()) {
            return base_url("images/course_images/noimage.png");
        }

        $newName = 'course_' . $thumbnail->getRandomName();
        $thumbnail->move($uploadPath, $newName);

        return base_url("images/course_images/" . $newName);
    }

    public function add($data) 
    {
        $this->verifyCourse($data["title"]);

        $id = $this->generateId("course");
        $data["id"] = $id;

        $slug = url_title($data["title"], '-', true);
        $data["slug"] = $slug;

        // encode before saving and decode before serving
        if (isset($data['topics']) && is_array($data['topics'])) {
            $data['topics'] = json_encode($data['topics']);
        }

        $data["thumbnail"] = $this->uploadThumbnail($data["thumbnail"] ?? null);

        $this->model->insert($data);
        return $id;
    }

    public function getAll()
    {
        $courses = $this->model->select("id, title, description, duration, level, thumbnail, created_at, updated_at")->findAll();

        if (empty($courses)) {
            return [];
        }

        return $courses;
    }
}
