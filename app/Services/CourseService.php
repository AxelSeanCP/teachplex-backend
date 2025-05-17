<?php

namespace App\Services;

use App\Exceptions\BadRequestError;
use App\Exceptions\NotFoundError;
use App\Models\Course;
use App\Models\Lesson;

class CourseService extends BaseService
{
    protected $model;
    protected $lessonModel;

    public function __construct(Course $courseModel, Lesson $lessonModel)
    {
        $this->model = $courseModel;
        $this->lessonModel = $lessonModel;
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

    public function get($id)
    {
        $course = $this->model
        ->select("id, title, description, duration, level, thumbnail, long_description, topics, created_at, updated_at")
        ->first($id);

        if (!$course) {
            throw new NotFoundError("Course not found");
        }

        if (isset($course["topics"]) && !is_null($course["topics"])) {
            $course["topics"] = json_decode($course["topics"]);
        }

        $lessons = $this->lessonModel
        ->where("course_id", $id)
        ->orderBy("lesson_order", "ASC")
        ->select("id, title")
        ->findAll();

        $course["lessons"] = $lessons ?? [];

        return $course;
    }

    public function edit($id, $data)
    {
        $course = $this->get($id);

        if (isset($data["thumbnail"])) {
            if (!empty($course["thumbnail"])) {
                $oldPath = $this->getLocalPathFromUrl($course["thumbnail"]);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $newName = "course_" . $data["thumbnail"]->getRandomName();
            $data["thumbnail"]->move(FCPATH. "images/course_images", $newName);

            $data["thumbnail"] = base_url('images/course_images/' . $newName);
        } else {
            unset($data["thumbnail"]);
        }
        
        // $this->verifyCourse($data["title"]);

        if (isset($data['topics']) && is_array($data['topics'])) {
            $data['topics'] = json_encode($data['topics']);
        }

        $this->model->update($id, $data);
    }

    public function remove($id)
    {
        $course = $this->get($id);

        if (!empty($course["thumbnail"])) {
            $oldPath = $this->getLocalPathFromUrl($course["thumbnail"]);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
        
        $this->model->delete($id);
    }

    private function getLocalPathFromUrl(string $url): string
    {
        $relativePath = str_replace(base_url(), '', $url);
        return FCPATH . ltrim($relativePath, '/');
    }
}
