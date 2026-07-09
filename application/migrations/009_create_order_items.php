<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_order_items extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ],
            'order_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => FALSE,
            ],
            'product_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => FALSE,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => FALSE,
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => FALSE,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('order_id');
        $this->dbforge->add_key('product_id');

        $this->dbforge->create_table('order_items');

        $this->db->query("
    ALTER TABLE order_items
    ADD CONSTRAINT fk_order_items_order
    FOREIGN KEY(order_id)
    REFERENCES orders(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
");


$this->db->query("
    ALTER TABLE order_items
    ADD CONSTRAINT fk_order_items_product
    FOREIGN KEY(product_id)
    REFERENCES products(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
");
    }

    public function down()
    {
        $this->dbforge->drop_table('order_items');
    }
}