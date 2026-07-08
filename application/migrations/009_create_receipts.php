<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_receipts extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('receipts')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'invoice_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'receipt_no' => [
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
        $this->dbforge->add_key('invoice_id');
        $this->dbforge->create_table('receipts', TRUE);

        $this->db->query('ALTER TABLE `receipts` ADD UNIQUE `uq_receipts_receipt_no` (`receipt_no`)');
        $this->db->query('ALTER TABLE `receipts` ADD CONSTRAINT `fk_receipts_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT');
    }

    public function down()
    {
        $this->dbforge->drop_table('receipts', TRUE);
    }
}
