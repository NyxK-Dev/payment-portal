<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_refunds extends CI_Migration
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
            'payment_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => FALSE,
            ],
            'refund_no' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE,
            ],
            'stripe_refund_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => FALSE,
            ],
            'reason' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'status_lookup_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => TRUE,
            ],
            'refunded_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => TRUE,
            ],
            'approved_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => TRUE,
            ],
            'approved_at' => [
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
        $this->dbforge->add_key('payment_id');
        $this->dbforge->add_key('status_lookup_id');
        $this->dbforge->add_key('created_by');
        $this->dbforge->add_key('approved_by');
        $this->dbforge->add_key('refund_no', FALSE, TRUE);
        $this->dbforge->add_key('stripe_refund_id', FALSE, TRUE);

        $this->dbforge->create_table('refunds');
        $this->db->query("
        ALTER TABLE refunds
        ADD CONSTRAINT fk_refunds_payment
        FOREIGN KEY(payment_id)
        REFERENCES payments(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
    ");


    $this->db->query("
        ALTER TABLE refunds
        ADD CONSTRAINT fk_refunds_status_lookup
        FOREIGN KEY(status_lookup_id)
        REFERENCES lookups(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
    ");


$this->db->query("
    ALTER TABLE refunds
    ADD CONSTRAINT fk_refunds_created_by
    FOREIGN KEY(created_by)
    REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
");


$this->db->query("
    ALTER TABLE refunds
    ADD CONSTRAINT fk_refunds_approved_by
    FOREIGN KEY(approved_by)
    REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
");
    }

    public function down()
    {
        $this->dbforge->drop_table('refunds');
    }
}