<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        $this->forge = \Config\Database::forge();

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'offer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'buyer_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'seller_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'property_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'default' => 'paid',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey('offer_id', 'offers', 'id', 'SET NULL', 'CASCADE', 'fk_payments_offer');
        $this->forge->addForeignKey('buyer_id', 'users', 'id', 'CASCADE', 'CASCADE', 'fk_payments_buyer');
        $this->forge->addForeignKey('seller_id', 'users', 'id', 'CASCADE', 'CASCADE', 'fk_payments_seller');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE', 'fk_payments_property');

        $this->forge->createTable('payments', true);
    }

    public function down()
    {
        $this->forge->dropTable('payments', true);
    }
}

