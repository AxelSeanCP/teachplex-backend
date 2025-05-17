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
            "user_id" => [
                "type" => "VARCHAR",
                "constraint" => 50,
                "null" => false
            ],
            "course_id" => [
                "type" => "VARCHAR",
                "constraint" => 50,
                "null" => false
            ],
            "pdf_url" => [
                "type" => "VARCHAR",
                "constraint" => 255,
            ],
            "created_at" => [
                "type" => "DATETIME",
                "null" => true
            ],
            "updated_at" => [
                "type" => "DATETIME",
                "null" => true
            ]
        ]);

        $this->forge->addPrimaryKey("id");
        $this->forge->addForeignKey("user_id", "users", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("course_id", "courses", "id", "CASCADE", "CASCADE");
        $this->forge->addUniqueKey(["user_id", "course_id"]);

        $this->forge->createTable("certificates");
    }

    public function down()
    {
        $this->forge->dropTable("certificates");
    }
}
