<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_payments extends CI_Migration
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
            'payment_no' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => FALSE,
            ],
            'currency' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'USD',
                'null' => FALSE,
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE,
            ],
            'status_lookup_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => TRUE,
            ],
            'version' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'null' => FALSE,
            ],
            'failure_reason' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'paid_at' => [
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
        $this->dbforge->add_key('payment_no', FALSE, TRUE);
        $this->dbforge->add_key('status_lookup_id');

        $this->dbforge->create_table('payments');
        $this->db->query("
    ALTER TABLE payments
    ADD CONSTRAINT fk_payments_order
    FOREIGN KEY(order_id)
    REFERENCES orders(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
");


$this->db->query("
    ALTER TABLE payments
    ADD CONSTRAINT fk_payments_status_lookup
    FOREIGN KEY(status_lookup_id)
    REFERENCES lookups(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
");
    }

    public function down()
    {
        $this->dbforge->drop_table('payments');
    }
}