<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBooksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "auto_increment" => true,
                "unsigned" => true
            ],
            "author_id" => [
                "type" => "INT",
                "unsigned" => true
            ],
            "name" => [
                "type" => "VARCHAR",
                "constraint" => 100,
                "null" => false
            ],
            "publication" => [
                "type" => "TEXT",
                "null" => true
            ],
            "cost" => [
                "type" => "INT",
                "unsigned" => true,
                "null" => false
            ],
            "created_at datetime default current_timestamp"
        ]);

        $this->forge->addPrimaryKey("id");

        $this->forge->createTable("books");
    }

    public function down()
    {
        $this->forge->dropTable("books");
    }
}
