<?php

namespace App\Services;

use App\Exceptions\BadRequestError;
use App\Exceptions\NotFoundError;
use App\Models\Lesson;

class LessonService extends BaseService
{
    protected $model;

    public function __construct(Lesson $lessonModel)
    {
        $this->model = $lessonModel;
    }

    public function verifyLesson($courseId, $title)
    {
        $lesson = $this->model->where("course_id", $courseId)->where("title", $title)->first();

        if ($lesson) {
            throw new BadRequestError("Lesson already exist");
        }
    }

    public function add($courseId, $data)
    {
        $this->verifyLesson($courseId, $data["title"]);

        $id = $this->generateId("lesson");
        $data["id"] = $id;
        $data["course_id"] = $courseId;

        $slug = url_title($data["title"], '-', true);
        $data["slug"] = $slug;

        $maxOrder = $this->model->where("course_id", $courseId)->selectMax("lesson_order")->get()->getRow()->lesson_order;
        $newOrder = $maxOrder ? $maxOrder + 1 : 1;
        $data["lesson_order"] = $newOrder;

        $this->model->insert($data);
        return $id;
    }

    public function get($lessonId)
    {
        $lesson = $this->model->where("id", $lessonId)
        ->select("id, title, content") //add video url and code text in the future
        ->first();

        if (empty($lesson)) {
            throw new NotFoundError("Lesson not found");
        }

        return $lesson;
    }

    public function edit($lessonId, $lessonData)
    {
        $this->get($lessonId);

        $this->model->update($lessonId, $lessonData);
    }

    public function remove($lessonId)
    {
        $this->get($lessonId);

        $this->model->delete($lessonId);
    }
}