<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthenticationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "token" => [
                "type" => "VARCHAR",
                "constraint" => 255,
            ],
        ]);
        $this->forge->addPrimaryKey("token");
        $this->forge->createTable("authentications");
    }

    public function down()
    {
        $this->forge->dropTable("authentications");
    }
}
