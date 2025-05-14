<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "VARCHAR",
                "constraint" => "50"
            ],
            "name" => [
                "type" => "VARCHAR",
                "constraint" => "255"
            ],
            "password" => [
                "type" => "VARCHAR",
                "constraint" => "255"
            ],
            "email" => [
                "type" => "VARCHAR",
                "constraint" => "255",
                "unique" => true
            ],
            "role" => [
                "type" => "ENUM",
                "constraint" => ["admin", "user"],
                "default" => "user",
            ],
            "created_at" => [
                "type" => "DATETIME",
            ],
            "updated_at" => [
                "type" => "DATETIME",
            ]
        ]);

        $this->forge->addPrimaryKey("id");
        $this->forge->createTable("users");
    }

    public function down()
    {
        $this->forge->dropTable("users");
    }
}
