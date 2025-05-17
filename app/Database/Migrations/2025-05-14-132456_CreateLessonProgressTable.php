<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLessonProgressTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "user_id" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "lesson_id" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "is_completed" => [
                "type" => "BOOLEAN",
                "default" => false,
            ],
            "completed_at" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "created_at" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "updated_at" => [
                "type" => "DATETIME",
                "null" => true
            ],
        ]);

        $this->forge->addPrimaryKey("id");
        $this->forge->addForeignKey("user_id", "users", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("lesson_id", "lessons", "id", "CASCADE", "CASCADE");
        $this->forge->addUniqueKey(["user_id", "lesson_id"]);

        $this->forge->createTable("lesson_progress");
    }

    public function down()
    {
        $this->forge->dropTable("lesson_progress");
    }
}
