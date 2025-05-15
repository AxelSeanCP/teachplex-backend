<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public array $users = [
        "name" => "required|string",
        "password" => "required|string",
        "email" => "required|string|valid_email|is_unique[users.email]",
    ];

    public array $login = [
        "email" => "required|string|valid_email",
        "password" => "required|string"
    ];

    public array $token = [
        "refreshToken" => "required|string"
    ];

    public array $course = [
        "title" => "required|string",
        "description" => "required|string",
        "thumbnail" => "if_exist|uploaded[thumbnail]|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png,image/webp]|max_size[thumbnail,2048]",
        "duration" => "required|string",
        "level" => "required|string|in_list[beginner,intermediate,advanced]",
        "long_description" => "if_exist|string",
        "topics.*" => "if_exist|string", 
    ];

    public array $enroll = [
        "courseId" => "required|string"
    ];

    public array $certificate = [
        "courseId" => "required|string",
    ];
}
