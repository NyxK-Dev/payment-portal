<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_stripe_webhook_events extends CI_Migration
{
    private function column_exists($table, $column)
    {
        $columns = $this->db->query('SHOW COLUMNS FROM `' . $table . '`')->result_array();
        foreach ($columns as $col) {
            if ($col['Field'] === $column) {
                return true;
            }
        }

        return false;
    }

    private function index_exists($table, $index_name)
    {
        $indexes = $this->db->query('SHOW INDEX FROM `' . $table . '`')->result_array();
        foreach ($indexes as $index) {
            if ($index['Key_name'] === $index_name) {
                return true;
            }
        }

        return false;
    }

    private function add_column_if_missing($table, $column, array $definition)
    {
        if ($this->column_exists($table, $column)) {
            return;
        }

        $this->dbforge->add_column($table, array($column => $definition));
    }

    private function add_index_if_missing($table, $index_name, $columns)
    {
        if ($this->index_exists($table, $index_name)) {
            return;
        }

        $this->db->query('ALTER TABLE `' . $table . '` ADD INDEX `' . $index_name . '` (' . implode(', ', array_map(function ($c) {
            return '`' . $c . '`';
        }, $columns)) . ')');
    }

    private function add_unique_if_missing($table, $index_name, $columns)
    {
        if ($this->index_exists($table, $index_name)) {
            return;
        }

        $this->db->query('ALTER TABLE `' . $table . '` ADD UNIQUE `' . $index_name . '` (' . implode(', ', array_map(function ($c) {
            return '`' . $c . '`';
        }, $columns)) . ')');
    }

    public function up()
    {
        if (!$this->db->table_exists('stripe_webhook_events')) {
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
                'retry_count' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                    'null'       => TRUE,
                ],
                'processing_started_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'processing_completed_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'error_message' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
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
            $this->dbforge->add_key('processed');
            $this->dbforge->add_key('event_type');
            $this->dbforge->create_table('stripe_webhook_events', TRUE);
        }

        $this->add_column_if_missing('stripe_webhook_events', 'retry_count', [
            'type'       => 'INT',
            'constraint' => 11,
            'default'    => 0,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('stripe_webhook_events', 'processing_started_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);
        $this->add_column_if_missing('stripe_webhook_events', 'processing_completed_at', [
            'type' => 'DATETIME',
            'null' => TRUE,
        ]);
        $this->add_column_if_missing('stripe_webhook_events', 'error_message', [
            'type' => 'TEXT',
            'null' => TRUE,
        ]);

        $this->add_index_if_missing('stripe_webhook_events', 'idx_stripe_webhook_events_processed', array('processed'));
        $this->add_index_if_missing('stripe_webhook_events', 'idx_stripe_webhook_events_event_type', array('event_type'));
        $this->add_unique_if_missing('stripe_webhook_events', 'uq_stripe_webhook_events_event_id', array('event_id'));
    }

    public function down()
    {
        $this->dbforge->drop_table('stripe_webhook_events', TRUE);
    }
}
