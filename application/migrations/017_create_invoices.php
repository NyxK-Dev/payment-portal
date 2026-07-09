<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_invoices extends CI_Migration
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
            'invoice_no' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => FALSE,
            ],
            'status_lookup_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => TRUE,
            ],
            'issued_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'issued_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
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
        $this->dbforge->add_key('status_lookup_id');
        $this->dbforge->add_key('issued_by');
        $this->dbforge->add_key('invoice_no', FALSE, TRUE);
        $this->dbforge->add_key('order_id', FALSE, TRUE);

        $this->dbforge->create_table('invoices');
        $this->db->query("
        ALTER TABLE invoices
        ADD CONSTRAINT fk_invoices_order
        FOREIGN KEY(order_id)
        REFERENCES orders(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
    ");


$this->db->query("
    ALTER TABLE invoices
    ADD CONSTRAINT fk_invoices_status_lookup
    FOREIGN KEY(status_lookup_id)
    REFERENCES lookups(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
");


$this->db->query("
    ALTER TABLE invoices
    ADD CONSTRAINT fk_invoices_issued_by
    FOREIGN KEY(issued_by)
    REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
");
    }

    public function down()
    {
        $this->dbforge->drop_table('invoices');
    }
}