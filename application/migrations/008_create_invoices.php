<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_invoices extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('invoices')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'order_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'invoice_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => FALSE,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => TRUE,
            ],
            'status_lookup_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
                'null'       => TRUE,
            ],
            'issued_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('order_id');
        $this->dbforge->create_table('invoices', TRUE);

        $this->db->query('ALTER TABLE `invoices` ADD UNIQUE `uq_invoices_invoice_no` (`invoice_no`)');
        $this->db->query('ALTER TABLE `invoices` ADD CONSTRAINT `fk_invoices_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT');
    }

    public function down()
    {
        $this->dbforge->drop_table('invoices', TRUE);
    }
}
