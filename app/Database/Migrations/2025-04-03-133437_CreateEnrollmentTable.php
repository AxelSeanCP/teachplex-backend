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
            "userId" => [
                "type" => "VARCHAR",
                "constraint" => 50,
                "null" => false,
            ],
            "courseId" => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "null" => false,
            ],
            "created_at" => [
                "type" => "DATETIME",
                "null" => "true"
            ],
            "updated_at" => [
                "type" => "DATETIME",
                "null" => "true"
            ]
        ]);

        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("userId", "users", "id", "CASCADE", "CASCADE");

        $this->forge->createTable("enrollments");
    }

    public function down()
    {
        $this->forge->dropTable("enrollments");
    }
}
