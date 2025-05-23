<?php

namespace App\Services;

use App\Exceptions\BadRequestError;
use App\Exceptions\ForbiddenError;
use App\Exceptions\NotFoundError;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;

class LessonService extends BaseService
{
    protected $model;
    protected $lessonProgressModel;
    protected $enrollmentModel;

    public function __construct(Lesson $lessonModel, LessonProgress $lessonProgressModel, Enrollment $enrollmentModel)
    {
        $this->model = $lessonModel;
        $this->lessonProgressModel = $lessonProgressModel;
        $this->enrollmentModel = $enrollmentModel;
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

        $lastLesson = $this->model
        ->where("course_id", $courseId)
        ->withDeleted()
        ->orderBy("lesson_order", "DESC")
        ->first();

        $maxOrder = 0;
        if ($lastLesson) {
            $maxOrder = (int) $lastLesson["lesson_order"] ?? 0;
        }

        $newOrder = $maxOrder + 1;
        $data["lesson_order"] = $newOrder;

        $this->model->insert($data);
        return $id;
    }

    public function get($userId, $courseId, $lessonId)
    {
        $lesson = $this->model->where("id", $lessonId)
        ->select("id, title, slug, content, lesson_order") //add video url and code text in the future
        ->first();

        if (empty($lesson)) {
            throw new NotFoundError("Lesson not found");
        }

        $isEnrolled = $this->enrollmentModel
        ->where("user_id", $userId)
        ->where("course_id", $courseId)
        ->first();

        if (empty($isEnrolled)) {
            throw new ForbiddenError("You are not enrolled in this course!");
        }

        if ($lesson["lesson_order"] <= 1) {
            return $lesson;
        }

        $previousLesson = $this->model
        ->select("id")
        ->where("course_id", $courseId)
        ->where('lesson_order <', $lesson["lesson_order"])
        ->orderBy("lesson_order", "DESC")
        ->first();

        if (!$previousLesson) {
            // probably the first active lesson, no previous lesson were found or already soft deleted
            // completion check will return false so return first
            return $lesson;
        }

        $completed = $this->lessonProgressModel
        ->where("user_id", $userId)
        ->where("lesson_id", $previousLesson["id"])
        ->where("is_completed", true)
        ->first();

        if (!$completed) {
            throw new ForbiddenError("You must complete the previous lesson first");
        }

        return $lesson;
    }

    public function checkAllLessonComplete($userId, $courseId)
    {
        $lessonIds = $this->model
        ->where("course_id", $courseId)
        ->select("id")
        ->findAll();

        if (empty($lessonIds)) {
            throw new NotFoundError("No lessons found in this course");
        }

        $lessonIdArray = array_column($lessonIds, 'id');

        $completedLessons = $this->lessonProgressModel
        ->where("user_id", $userId)
        ->whereIn("lesson_id", $lessonIdArray)
        ->where("is_completed", true)
        ->select("lesson_id")
        ->findAll();

        $completedLessonArray = array_column($completedLessons, 'lesson_id');

        $uncompleted = array_diff($lessonIdArray, $completedLessonArray);

        if (!empty($uncompleted)) {
            throw new BadRequestError("You must complete all lessons in this course.");
        }
    }

    public function completeLesson($userId, $lessonId)
    {
        $id = $this->generateId("lesson_progress");
        $data = [
            "id" => $id,
            "user_id" => $userId,
            "lesson_id" => $lessonId,
            "is_completed" => true,
            "completed_at" => date("Y-m-d H:i:s"),
        ];

        $existing = $this->lessonProgressModel
        ->where("user_id", $userId)
        ->where("lesson_id", $lessonId)
        ->first();

        if ($existing) {
            $this->lessonProgressModel->update($existing["id"], $data);
        } else {
            $this->lessonProgressModel->insert($data);
        }
    }

    public function edit($lessonId, $lessonData)
    {
        $lesson = $this->model->where("id", $lessonId)->first();

        if (empty($lesson)) {
            throw new NotFoundError("Lesson not found");
        }

        $this->model->update($lessonId, $lessonData);
    }

    public function remove($lessonId)
    {
        $lesson = $this->model->where("id", $lessonId)->first();

        if (empty($lesson)) {
            throw new NotFoundError("Lesson not found");
        }

        $this->model->delete($lessonId);
    }
}