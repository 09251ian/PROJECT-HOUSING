<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFavoritesTable extends Migration
{
    public function up()
    {
        $this->forge = \Config\Database::forge();

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'buyer_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'property_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addUniqueKey(['buyer_id', 'property_id'], 'uniq_buyer_property_favorite');

        $this->forge->addForeignKey(
            'buyer_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE',
            'fk_favorites_buyer'
        );
        $this->forge->addForeignKey(
            'property_id',
            'properties',
            'id',
            'CASCADE',
            'CASCADE',
            'fk_favorites_property'
        );

        $this->forge->createTable('favorites', true);
    }

    public function down()
    {
        $this->forge->dropTable('favorites', true);
    }
}

