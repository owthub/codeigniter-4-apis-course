<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomColumnsUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn("users", [
            "gender" => [
                "type" => "ENUM",
                "constraint" => ["male", "female", "others"],
                "null" => true
            ],
            "phone_no" => [
                "type" => "VARCHAR",
                "constraint" => 25,
                "null" => true
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn("users", ["gender", "phone_no"]);
    }
}
