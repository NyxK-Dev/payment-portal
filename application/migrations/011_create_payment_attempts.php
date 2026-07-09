<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_payment_attempts extends CI_Migration
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
            'attempt_no' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
            ],
            'provider' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE,
            ],
            'stripe_session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'payment_intent_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => TRUE,
            ],
            'status_lookup_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => TRUE,
            ],
            'failure_reason' => [
                'type' => 'TEXT',
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
        $this->dbforge->create_table('payment_attempts');

        $this->db->query("
            ALTER TABLE payment_attempts
            ADD UNIQUE KEY uq_payment_attempt (payment_id, attempt_no)
        ");
        $this->db->query("
    ALTER TABLE payment_attempts
    ADD CONSTRAINT fk_payment_attempts_payment
    FOREIGN KEY(payment_id)
    REFERENCES payments(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
");


$this->db->query("
    ALTER TABLE payment_attempts
    ADD CONSTRAINT fk_payment_attempts_status_lookup
    FOREIGN KEY(status_lookup_id)
    REFERENCES lookups(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
");
    }

    public function down()
    {
        $this->dbforge->drop_table('payment_attempts');
    }
}