<?php

namespace Config;

use App\Models\Authentication;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use CodeIgniter\Config\BaseService;
use App\Services\UserService;
use App\Services\AuthenticationService;
use App\Services\CourseService;
use App\Services\LessonService;
use App\Services\EnrollmentService;
use App\Services\CertificateService;
use App\Services\UserContext;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    public static function userContext($getShared = true)
    {
        return $getShared ? static::getSharedInstance("userContext") : new UserContext();
    }

    public static function userService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance(__FUNCTION__);
        }

        return new UserService(new User());
    }

    public static function authenticationService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance(__FUNCTION__);
        }

        return new authenticationService(new Authentication());
    }

    public static function courseService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance(__FUNCTION__);
        }

        return new courseService(new Course(), new Lesson());
    }
    
    public static function lessonService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance(__FUNCTION__);
        }

        return new lessonService(new Lesson());
    }

    public static function enrollmentService($getShared = true) 
    {
        if ($getShared) {
            return static::getSharedInstance(__FUNCTION__);
        }

        return new enrollmentService(new Enrollment());
    }

    public static function certificateService($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance(__FUNCTION__);
        }

        $userService = static::userService();
        $enrollmentService = static::enrollmentService();

        return new certificateService(new Certificate(), $userService, $enrollmentService);
    }
}
