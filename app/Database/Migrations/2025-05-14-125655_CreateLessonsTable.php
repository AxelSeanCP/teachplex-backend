<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLessonsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "course_id" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "title" => [
                "type" => "VARCHAR",
                "constraint" => 255,
            ],
            "slug" => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "null" => false,
                "unique" => true,
            ],
            'video_url' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'code_text' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            "content" => [
                "type" => "TEXT",
            ],
            "lesson_order" => [
                "type" => "INT",
                "null" => true,
                "default" => 0,
            ],
            "created_at" => [
                "type" => "DATETIME",
            ],
            "updated_at" => [
                "type" => "DATETIME",
            ]
        ]);

        $this->forge->addPrimaryKey("id");
        $this->forge->addForeignKey("course_id", "courses", "id", "CASCADE", "CASCADE");

        $this->forge->createTable("lessons");
    }

    public function down()
    {
        $this->forge->dropTable("lessons");
    }
}
