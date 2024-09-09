<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthorsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "auto_increment" => true,
                "unsigned" => true
            ],
            "name" => [
                "type" => "VARCHAR",
                "constraint" => 120,
                "null" => false
            ],
            "email" => [
                "type" => "VARCHAR",
                "constraint" => 80,
                "null" => false
            ],
            "password" => [
                "type" => "VARCHAR",
                "constraint" => 80,
                "null" => false
            ],
            "phone_no" => [
                "type" => "VARCHAR",
                "constraint" => 30,
                "null" => true
            ],
            "created_at datetime default current_timestamp"
        ]);

        $this->forge->addPrimaryKey("id");

        $this->forge->createTable("authors");
    }

    public function down()
    {
        $this->forge->dropTable("authors");
    }
}
