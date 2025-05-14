<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnrollmentTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "VARCHAR",
                "constraint" => 50
            ],
            "user_id" => [
                "type" => "VARCHAR",
                "constraint" => 50,
                "null" => false,
            ],
            "course_id" => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "null" => false,
            ],
            "created_at" => [
                "type" => "DATETIME",
            ],
            "updated_at" => [
                "type" => "DATETIME",
            ]
        ]);

        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("user_id", "users", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("course_id", "courses", "id", "CASCADE", "CASCADE");
        $this->forge->addUniqueKey(["user_id", "course_id"]);

        $this->forge->createTable("enrollments");
    }

    public function down()
    {
        $this->forge->dropTable("enrollments");
    }
}
