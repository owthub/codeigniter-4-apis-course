<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "auto_increment" => true,
                "unsigned" => true
            ],
            "title" => [
                "type" => "VARCHAR",
                "constraint" => 120,
                "null" => false
            ],
            "cost" => [
                "type" => "INT",
                "null" => false
            ],
            "description" => [
                "type" => "TEXT",
                "null" => true
            ],
            "product_image" => [
                "type" => "text",
                "null" => true
            ],
            "created_at datetime default current_timestamp"
        ]);

        $this->forge->addPrimaryKey("id");

        $this->forge->createTable("products");
    }

    public function down()
    {
        $this->forge->dropTable("products");
    }
}
