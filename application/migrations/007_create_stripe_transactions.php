<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_stripe_transactions extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('stripe_transactions')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'payment_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'stripe_session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
            ],
            'payment_intent_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
            ],
            'stripe_webhook_event_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => TRUE,
                'null'       => TRUE,
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => TRUE,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => TRUE,
            ],
            'payment_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => TRUE,
            ],
            'raw_payload' => [
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
        $this->dbforge->add_key('payment_id');
        $this->dbforge->create_table('stripe_transactions', TRUE);

        $this->db->query('ALTER TABLE `stripe_transactions` ADD UNIQUE `uq_stripe_transactions_session_id` (`stripe_session_id`)');
        $this->db->query('ALTER TABLE `stripe_transactions` ADD UNIQUE `uq_stripe_transactions_intent_id` (`payment_intent_id`)');
        $this->db->query('ALTER TABLE `stripe_transactions` ADD CONSTRAINT `fk_stripe_transactions_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT');
    }

    public function down()
    {
        $this->dbforge->drop_table('stripe_transactions', TRUE);
    }
}
