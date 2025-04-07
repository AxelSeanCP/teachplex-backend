<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsCompletedToEnrollments extends Migration
{
    public function up()
    {
        $fields = [
            "isCompleted" => [
                "type" => "BOOL",
                "null" => false,
                "default" => 0
            ]
        ];
        $this->forge->addColumn("enrollments", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn("enrollments", "isCompleted");
    }
}
