<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Enrollment;
use App\Exceptions\ForbiddenError;
use App\Exceptions\NotFoundError;

class CourseProgressService extends BaseService
{
    protected $courseModel;
    protected $lessonModel;
    protected $lessonProgressModel;
    protected $courseProgressModel;
    protected $enrollmentModel;

    public function __construct(
        Course $courseModel,
        Lesson $lessonModel,
        CourseProgress $courseProgressModel,
        LessonProgress $lessonProgressModel,
        Enrollment $enrollmentModel,
    ) {
        $this->courseModel = $courseModel;
        $this->lessonModel = $lessonModel;
        $this->lessonProgressModel = $lessonProgressModel;
        $this->courseProgressModel = $courseProgressModel;
        $this->enrollmentModel = $enrollmentModel;
    }

    public function getCourseProgress(string $userId, string $courseId): array
    {
        $course = $this->courseModel->find($courseId);
        if (empty($course)) {
            throw new NotFoundError("Course not found");
        }

        $isEnrolled = $this->enrollmentModel
        ->where("user_id", $userId)
        ->where("course_id", $courseId)
        ->first();

        if (empty($isEnrolled)) {
            throw new ForbiddenError("You are not enrolled in this course");
        }

        $lessons = $this->lessonModel
        ->where("course_id", $courseId)
        ->where("deleted_at", null)
        ->orderBy("lesson_order", "ASC")
        ->findAll();

        $totalLessons = count($lessons);
        $completedLessonCount = 0;
        $lessonProgressDetails = [];

        $userLessonProgress = $this->lessonProgressModel
        ->where("user_id", $userId)
        ->whereIn("lesson_id", array_column($lessons, "id"))
        ->findAll();

        $lessonProgressMap = [];
        foreach ($userLessonProgress as $progress) {
            $lessonProgressMap[$progress["lesson_id"]] = $progress;
        }

        foreach ($lessons as $lesson) {
            $isLessonCompleted = false;
            $lessonCompletedAt = null;

            if (isset($lessonProgressMap[$lesson["id"]])) {
                $progressRecord = $lessonProgressMap[$lesson["id"]];
                if ($progressRecord["is_completed"]) {
                    $isLessonCompleted = true;
                    $lessonCompletedAt = $progressRecord["completed_at"];
                    $completedLessonCount++;
                }
            }

            $lessonProgressDetails[] = [
                "id" => $lesson["id"],
                "title" => $lesson["title"],
                "slug" => $lesson["slug"],
                "lesson_order" => (int) $lesson["lesson_order"],
                "is_completed" => $isLessonCompleted,
                "completed_at" => $lessonCompletedAt,
            ];
        }

        $progressPercentage = $totalLessons > 0 
        ? round(($completedLessonCount / $totalLessons) * 100)
        : 0;

        $overallCourseProgress = $this->courseProgressModel
        ->where("user_id", $userId)
        ->where("course_id", $courseId)
        ->first();

        $isOverallCourseCompleted = false;
        $overallCourseCompletedAt = null;

        if ($overallCourseProgress && $overallCourseProgress["completed_at"]) {
            $isOverallCourseCompleted = true;
            $overallCourseCompletedAt = $overallCourseProgress["completed_at"];
        }

        return [
            "course_id" => $course["id"],
            "course_title" => $course["title"],
            "is_enrolled" => true,
            "overall_course_completed" => $isOverallCourseCompleted,
            "overall_course_completed_at" => $overallCourseCompletedAt,
            "total_lessons" => $totalLessons,
            "completed_lessons_count" => $completedLessonCount,
            "progress_percentage" => $progressPercentage,
            "lessons" => $lessonProgressDetails,
        ];
    }
}