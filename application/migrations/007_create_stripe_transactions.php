<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_stripe_transactions extends CI_Migration
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

    private function foreign_key_exists($table, $constraint_name)
    {
        $result = $this->db->query('SHOW CREATE TABLE `' . $table . '`')->row_array();
        return isset($result['Create Table']) && strpos($result['Create Table'], $constraint_name) !== false;
    }

    private function add_foreign_key_if_missing($table, $constraint_name, $column, $reference_table, $reference_column, $on_update = 'CASCADE', $on_delete = 'RESTRICT')
    {
        if ($this->foreign_key_exists($table, $constraint_name) || !$this->db->table_exists($reference_table)) {
            return;
        }

        $this->db->query('ALTER TABLE `' . $table . '` ADD CONSTRAINT `' . $constraint_name . '` FOREIGN KEY (`' . $column . '`) REFERENCES `' . $reference_table . '` (`' . $reference_column . '`) ON UPDATE ' . $on_update . ' ON DELETE ' . $on_delete);
    }

    public function up()
    {
        if (!$this->db->table_exists('stripe_transactions')) {
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
                'payment_attempt_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => TRUE,
                    'null'       => TRUE,
                ],
                'webhook_event_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => TRUE,
                    'null'       => TRUE,
                ],
                'provider' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'stripe',
                    'null'       => TRUE,
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
                'charge_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
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
                'provider_status' => [
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
            $this->dbforge->add_key('payment_attempt_id');
            $this->dbforge->add_key('webhook_event_id');
            $this->dbforge->add_key('payment_intent_id');
            $this->dbforge->add_key('stripe_session_id');
            $this->dbforge->add_key('charge_id');
            $this->dbforge->create_table('stripe_transactions', TRUE);
        }

        $this->add_column_if_missing('stripe_transactions', 'payment_attempt_id', [
            'type'       => 'BIGINT',
            'constraint' => 20,
            'unsigned'   => TRUE,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('stripe_transactions', 'webhook_event_id', [
            'type'       => 'BIGINT',
            'constraint' => 20,
            'unsigned'   => TRUE,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('stripe_transactions', 'provider', [
            'type'       => 'VARCHAR',
            'constraint' => 50,
            'default'    => 'stripe',
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('stripe_transactions', 'charge_id', [
            'type'       => 'VARCHAR',
            'constraint' => 255,
            'null'       => TRUE,
        ]);
        $this->add_column_if_missing('stripe_transactions', 'provider_status', [
            'type'       => 'VARCHAR',
            'constraint' => 50,
            'null'       => TRUE,
        ]);

        $this->add_index_if_missing('stripe_transactions', 'idx_stripe_transactions_payment_attempt_id', array('payment_attempt_id'));
        $this->add_index_if_missing('stripe_transactions', 'idx_stripe_transactions_webhook_event_id', array('webhook_event_id'));
        $this->add_index_if_missing('stripe_transactions', 'idx_stripe_transactions_payment_intent_id', array('payment_intent_id'));
        $this->add_index_if_missing('stripe_transactions', 'idx_stripe_transactions_stripe_session_id', array('stripe_session_id'));
        $this->add_index_if_missing('stripe_transactions', 'idx_stripe_transactions_charge_id', array('charge_id'));
        $this->add_foreign_key_if_missing('stripe_transactions', 'fk_stripe_transactions_payment', 'payment_id', 'payments', 'id', 'CASCADE', 'RESTRICT');
        $this->add_foreign_key_if_missing('stripe_transactions', 'fk_stripe_transactions_payment_attempt', 'payment_attempt_id', 'payment_attempts', 'id', 'CASCADE', 'SET NULL');
        $this->add_foreign_key_if_missing('stripe_transactions', 'fk_stripe_transactions_webhook_event', 'webhook_event_id', 'stripe_webhook_events', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        $this->dbforge->drop_table('stripe_transactions', TRUE);
    }
}
