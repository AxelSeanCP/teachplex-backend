<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "VARCHAR",
                "constraint" => 50
            ],
            "title" => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "null" => false,
            ],
            "slug" => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "null" => false,
                "unique" => true,
            ],
            "description" => [
                "type" => "VARCHAR",
                "constraint" => 255,
            ],
            "thumbnail" => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "default" => "noimage.png"
            ],
            "duration" => [
                "type" => "VARCHAR",
                "constraint" => 25,
            ],
            "level" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "long_description" => [
                "type" => "TEXT",
                "null" => true,
            ],
            "topics" => [
                "type" => "TEXT",
                "null" => true,
            ],
            "deleted_at" => [
                "type" => "DATETIME",
            ],
            "created_at" => [
                "type" => "DATETIME",
            ],
            "updated_at" => [
                "type" => "DATETIME",
            ]
        ]);

        $this->forge->addPrimaryKey("id");
        $this->forge->createTable("courses");
    }

    public function down()
    {
        $this->forge->dropTable("courses");
    }
}
