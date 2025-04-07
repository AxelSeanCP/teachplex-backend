<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCertificatesTable extends Migration
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
                "null" => false
            ],
            "courseId" => [
                "type" => "VARCHAR",
                "constraint" => 50,
                "null" => false
            ],
            "courseName" => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "null" => true,
            ],
            "pdfUrl" => [
                "type" => "VARCHAR",
                "constraint" => 255,
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

        $this->forge->addPrimaryKey("id");
        $this->forge->addForeignKey("userId", "users", "id", "CASCADE", "CASCADE");

        $this->forge->createTable("certificates");
    }

    public function down()
    {
        $this->forge->dropTable("certificates");
    }
}
