<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCourseSlugIndex extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE courses DROP INDEX slug");

        $this->db->query("CREATE UNIQUE INDEX unique_slug_active ON courses (slug, deleted_at)");
    }

    public function down()
    {
        $this->db->query("DROP INDEX unique_slug_active ON courses");

        $this->db->query("CREATE UNIQUE INDEX slug ON courses (slug)");
    }
}
