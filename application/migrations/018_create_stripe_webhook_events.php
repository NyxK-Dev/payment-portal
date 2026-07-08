<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_stripe_webhook_events extends CI_Migration
{
    public function up()
    {
        if ($this->db->table_exists('stripe_webhook_events')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'event_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => FALSE,
            ],
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
            ],
            'processed' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => FALSE,
            ],
            'payload' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('stripe_webhook_events', TRUE);
        $this->db->query('ALTER TABLE `stripe_webhook_events` ADD UNIQUE `uq_stripe_webhook_events_event_id` (`event_id`)');
    }

    public function down()
    {
        $this->dbforge->drop_table('stripe_webhook_events', TRUE);
    }
}
